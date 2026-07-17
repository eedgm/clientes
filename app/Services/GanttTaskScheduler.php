<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\Task;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GanttTaskScheduler
{
    /**
     * Default hours-per-day used when a proposal has no version yet
     * or its latest version omits the setting.
     */
    public const DEFAULT_HOUR_PER_DAY = 8;

    /**
     * Resolve the hour-per-day for a proposal from its latest version
     * (or any fallback). The result is always > 0 because the model
     * and the calculator both validate the column as numeric.
     */
    public function hourPerDayFor(Proposal $proposal): int
    {
        $latest = $proposal->versions()->latest('id')->first();

        if (! $latest instanceof Version) {
            return self::DEFAULT_HOUR_PER_DAY;
        }

        $raw = $latest->hour_per_day;

        if ($raw === null || $raw === '' || (float) $raw <= 0) {
            return self::DEFAULT_HOUR_PER_DAY;
        }

        return max(1, (int) round((float) $raw));
    }

    /**
     * Convert task hours into a day duration using
     * `ceil(hours / hour_per_day)`. Hours < hourPerDay still produce
     * a 1-day duration so the gantt always shows at least one bar.
     */
    public function durationFromHours(float $hours, int $hourPerDay): int
    {
        if ($hourPerDay <= 0) {
            $hourPerDay = self::DEFAULT_HOUR_PER_DAY;
        }

        if ($hours <= 0) {
            return 0;
        }

        return (int) ceil($hours / $hourPerDay);
    }

    /**
     * Prepare a payload for creating a gantt task. Always derives
     * the duration from hours using the proposal's hour_per_day
     * (the gantt config passes the latest version's value) and
     * assigns the next sort_order for the proposal. When the
     * proposal already has ordered tasks, the new task is also
     * auto-placed on the next business day after the last one so
     * the user does not have to drag it manually.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function prepareForCreate(Proposal $proposal, array $payload): array
    {
        $hourPerDay = $this->hourPerDayFor($proposal);
        $payload['hour_per_day'] = $hourPerDay;
        $payload['proposal_id'] = $proposal->id;

        $hours = isset($payload['hours']) ? (float) $payload['hours'] : 0.0;
        $payload['duration'] = $this->durationFromHours($hours, $hourPerDay);

        if (! isset($payload['sort_order']) || $payload['sort_order'] === null) {
            $payload['sort_order'] = $this->nextSortOrderFor($proposal);
        }

        $lastOrdered = $this->lastOrderedTaskFor($proposal);

        if ($lastOrdered) {
            $payload['start_date'] = $this->nextBusinessDay(
                $this->endDateFor($lastOrdered)
            )->format('Y-m-d H:i:s');
        }

        return $payload;
    }

    /**
     * Prepare a payload for updating a gantt task. Re-derives the
     * duration from hours when the client changes hours, and keeps
     * the sort_order untouched unless the caller passes one.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function prepareForUpdate(Proposal $proposal, Task $task, array $payload): array
    {
        $hourPerDay = $this->hourPerDayFor($proposal);
        $payload['hour_per_day'] = $hourPerDay;

        if (array_key_exists('hours', $payload)) {
            $payload['duration'] = $this->durationFromHours(
                (float) ($payload['hours'] ?? 0),
                $hourPerDay
            );
        }

        return $payload;
    }

    /**
     * Cascade the schedule of a task to every task that comes AFTER
     * it in the persisted order on the same proposal. Following tasks
     * are pushed forward so they start the day after the previous
     * task ends.
     *
     * Tasks that come before the cursor are intentionally left alone:
     * walking over them would compute an "expected start" anchored to
     * the cursor's end date, which is later in the calendar and would
     * reorder earlier tasks to a later position, breaking chronology.
     */
    public function cascadeSchedule(Task $task): Collection
    {
        $proposal = $task->proposal()->first();

        if (! $proposal instanceof Proposal) {
            return collect();
        }

        $ordered = $this->orderedTasksFor($proposal);
        $changed = collect();

        $cursorIndex = $ordered
            ->search(fn (Task $candidate) => $candidate->id === $task->id);

        if ($cursorIndex === false) {
            return $changed;
        }

        $cursor = $ordered->get($cursorIndex);

        $previousEnd = $this->endDateFor($cursor);

        $following = $ordered->slice($cursorIndex + 1)->values();

        foreach ($following as $candidate) {
            $expectedStart = $this->nextBusinessDay($previousEnd);

            if ($this->shouldShift($candidate, $expectedStart)) {
                $candidate->start_date = $expectedStart->format('Y-m-d H:i:s');
                $candidate->save();
                $changed->push($candidate);
            }

            $previousEnd = $this->endDateFor($candidate);
        }

        return $changed;
    }

    /**
     * Persist a custom ordering for a proposal's tasks. Tasks are
     * matched by id, and the sort_order is rewritten starting at 1 in
     * the provided order. Missing tasks keep their existing order.
     *
     * @param  array<int, int|string>  $orderedTaskIds
     */
    public function applyOrdering(Proposal $proposal, array $orderedTaskIds): Collection
    {
        $current = $this->orderedTasksFor($proposal)->keyBy('id');
        $used = [];
        $position = 1;

        foreach ($orderedTaskIds as $rawId) {
            $taskId = (int) $rawId;

            if (! $current->has($taskId) || isset($used[$taskId])) {
                continue;
            }

            $task = $current->get($taskId);

            if ((int) $task->sort_order !== $position) {
                $task->sort_order = $position;
                $task->save();
            }

            $used[$taskId] = true;
            $position++;
        }

        return $current->values();
    }

    /**
     * Tasks for a proposal ordered by manual sort_order, falling back
     * to id so newly-created rows are appended.
     *
     * @return Collection<int, Task>
     */
    public function orderedTasksFor(Proposal $proposal): Collection
    {
        return $proposal->tasks()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function nextSortOrderFor(Proposal $proposal): int
    {
        return ((int) $proposal->tasks()->max('sort_order')) + 1;
    }

    /**
     * Return the last task that has a real sort_order (> 0), skipping
     * rows that only carry the default 0 placeholder. Used by
     * `prepareForCreate` to decide where the new task should be
     * auto-placed on the schedule.
     */
    public function lastOrderedTaskFor(Proposal $proposal): ?Task
    {
        return $this->orderedTasksFor($proposal)
            ->where('sort_order', '>', 0)
            ->last();
    }

    private function endDateFor(Task $task): Carbon
    {
        $start = $this->parseDate($task->start_date);

        $duration = max(0, (int) $task->duration);

        return $start->copy()->addDays($duration);
    }

    private function parseDate($value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value->copy();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance(\DateTime::createFromInterface($value));
        }

        return Carbon::parse((string) $value);
    }

    private function shouldShift(Task $task, Carbon $expectedStart): bool
    {
        $current = $this->parseDate($task->start_date);

        return $current->lt($expectedStart);
    }

    private function nextBusinessDay(Carbon $date): Carbon
    {
        $next = $date->copy()->addDay();

        while ($next->isWeekend()) {
            $next->addDay();
        }

        return $next->startOfDay();
    }
}
