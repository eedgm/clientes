<?php

namespace Tests\Feature\Services;

use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Statu;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use App\Services\GanttTaskScheduler;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GanttTaskSchedulerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'email' => 'admin@admin.com',
        ]));

        $this->seed(PermissionsSeeder::class);
    }

    /** @test */
    public function it_defaults_to_eight_hours_per_day_when_proposal_has_no_version()
    {
        $proposal = Proposal::factory()->create();

        $scheduler = app(GanttTaskScheduler::class);

        $this->assertSame(8, $scheduler->hourPerDayFor($proposal));
    }

    /** @test */
    public function it_uses_latest_version_hour_per_day_when_present()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 6,
        ]);
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 4,
        ]);

        $scheduler = app(GanttTaskScheduler::class);

        $this->assertSame(4, $scheduler->hourPerDayFor($proposal));
    }

    /** @test */
    public function it_falls_back_to_eight_when_version_hour_per_day_is_invalid()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 0,
        ]);

        $scheduler = app(GanttTaskScheduler::class);

        $this->assertSame(8, $scheduler->hourPerDayFor($proposal));
    }

    /** @test */
    public function it_converts_task_hours_to_duration_using_ceil_division()
    {
        $scheduler = app(GanttTaskScheduler::class);

        $this->assertSame(1, $scheduler->durationFromHours(4.0, 8));
        $this->assertSame(1, $scheduler->durationFromHours(8.0, 8));
        $this->assertSame(2, $scheduler->durationFromHours(9.0, 8));
        $this->assertSame(3, $scheduler->durationFromHours(17.0, 8));
        $this->assertSame(0, $scheduler->durationFromHours(0.0, 8));
    }

    /** @test */
    public function it_keeps_duration_at_least_one_when_hours_below_day_capacity()
    {
        $scheduler = app(GanttTaskScheduler::class);

        $this->assertSame(1, $scheduler->durationFromHours(2.0, 8));
    }

    /** @test */
    public function it_derives_duration_from_hours_when_creating_gantt_task()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $payload = $this->basePayload($proposal, [
            'hours' => 20,
        ]);

        $scheduler = app(GanttTaskScheduler::class);
        $prepared = $scheduler->prepareForCreate($proposal, $payload);

        $this->assertSame(3, $prepared['duration']);
        $this->assertSame(1, $prepared['sort_order']);
    }

    /** @test */
    public function it_appends_to_the_end_of_proposal_order_on_each_create()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $scheduler = app(GanttTaskScheduler::class);

        $first = Task::create(
            $scheduler->prepareForCreate($proposal, $this->basePayload($proposal))
        );
        $second = Task::create(
            $scheduler->prepareForCreate($proposal, $this->basePayload($proposal))
        );
        $third = Task::create(
            $scheduler->prepareForCreate($proposal, $this->basePayload($proposal))
        );

        $this->assertSame(1, (int) $first->sort_order);
        $this->assertSame(2, (int) $second->sort_order);
        $this->assertSame(3, (int) $third->sort_order);
    }

    /** @test */
    public function it_cascades_schedule_to_following_tasks_when_a_task_starts_later()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $first = $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        $second = $this->createTaskFor($proposal, '2026-01-06', 2, 2);
        $third = $this->createTaskFor($proposal, '2026-01-08', 1, 3);

        $first->update([
            'start_date' => '2026-01-12 00:00:00',
            'duration' => 4,
        ]);

        $changed = app(GanttTaskScheduler::class)->cascadeSchedule($first->fresh());

        $this->assertCount(2, $changed);
        $this->assertSame('2026-01-19 00:00:00', $second->fresh()->start_date->format('Y-m-d H:i:s'));
        $this->assertSame('2026-01-22 00:00:00', $third->fresh()->start_date->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_does_not_shift_tasks_that_come_before_the_cursor()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        // The middle task is the cursor. A buggy implementation that
        // iterates over every task except the cursor would walk over
        // the first task, anchor `previousEnd` on the cursor's new end
        // date, and shift the first task AFTER the cursor — breaking
        // chronology. The correct behavior is to leave the first task
        // untouched.
        $first = $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        $middle = $this->createTaskFor($proposal, '2026-01-06', 1, 2);
        $third = $this->createTaskFor($proposal, '2026-01-07', 1, 3);

        $middle->update([
            'start_date' => '2026-01-20 00:00:00',
            'duration' => 2,
        ]);

        $changed = app(GanttTaskScheduler::class)->cascadeSchedule($middle->fresh());

        $this->assertCount(1, $changed);
        $this->assertSame($third->id, $changed->first()->id);
        $this->assertSame('2026-01-05 00:00:00', $first->fresh()->start_date->format('Y-m-d H:i:s'));
        $this->assertSame('2026-01-23 00:00:00', $third->fresh()->start_date->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_skips_cascade_when_a_following_task_is_already_later()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $first = $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        $second = $this->createTaskFor($proposal, '2026-02-01', 2, 2);

        $first->update(['start_date' => '2026-01-12 00:00:00']);

        $changed = app(GanttTaskScheduler::class)->cascadeSchedule($first->fresh());

        $this->assertCount(0, $changed);
        $this->assertSame('2026-02-01 00:00:00', $second->fresh()->start_date->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_skips_weekends_when_cascading_schedule()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $first = $this->createTaskFor($proposal, '2026-01-08', 2, 1); // Thu-Fri
        $second = $this->createTaskFor($proposal, '2026-01-12', 1, 2); // Mon

        $first->update(['start_date' => '2026-01-15 00:00:00']); // Thu, duration 2

        app(GanttTaskScheduler::class)->cascadeSchedule($first->fresh());

        // First task ends Sat 2026-01-17; next business day is Mon 2026-01-19.
        $this->assertSame('2026-01-19 00:00:00', $second->fresh()->start_date->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_recomputes_duration_from_hours_on_update_using_proposal_hour_per_day()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 6,
        ]);

        $task = $this->createTaskFor($proposal, '2026-01-05', 1, 1);

        $scheduler = app(GanttTaskScheduler::class);
        $scheduler->prepareForUpdate($proposal, $task, ['hours' => 13]);

        $task->update(['hours' => 13, 'duration' => $scheduler->durationFromHours(13.0, 6)]);

        $this->assertSame(3, (int) $task->fresh()->duration);
    }

    /** @test */
    public function it_persists_manual_ordering_for_a_proposal()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $first = $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        $second = $this->createTaskFor($proposal, '2026-01-06', 1, 2);
        $third = $this->createTaskFor($proposal, '2026-01-07', 1, 3);

        app(GanttTaskScheduler::class)->applyOrdering($proposal, [
            $third->id,
            $first->id,
            $second->id,
        ]);

        $ordered = $proposal->orderedTasks()->get();

        $this->assertSame([$third->id, $first->id, $second->id], $ordered->pluck('id')->all());
    }

    /** @test */
    public function it_ignores_unknown_or_duplicate_ids_when_persisting_ordering()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $first = $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        $second = $this->createTaskFor($proposal, '2026-01-06', 1, 2);

        app(GanttTaskScheduler::class)->applyOrdering($proposal, [
            999999,
            $second->id,
            $first->id,
            $first->id,
        ]);

        $ordered = $proposal->orderedTasks()->get();

        $this->assertSame([$second->id, $first->id], $ordered->pluck('id')->all());
    }

    /** @test */
    public function it_auto_places_new_tasks_after_the_last_ordered_task()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        // Mon 2026-01-05, duration 1 -> ends Tue 2026-01-06
        $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        // Tue 2026-01-06, duration 2 -> ends Thu 2026-01-08
        $this->createTaskFor($proposal, '2026-01-06', 2, 2);

        $payload = $this->basePayload($proposal, [
            // The user-entered start_date must be overridden by the
            // scheduler to land after the last ordered task.
            'start_date' => '2026-01-01 00:00:00',
        ]);

        $prepared = app(GanttTaskScheduler::class)->prepareForCreate($proposal, $payload);

        // Last ordered task ends Thu 2026-01-08; next business day is Fri 2026-01-09.
        $this->assertSame('2026-01-09 00:00:00', $prepared['start_date']);
        $this->assertSame(3, $prepared['sort_order']);
    }

    /** @test */
    public function it_keeps_the_user_start_date_when_no_ordered_tasks_exist()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $payload = $this->basePayload($proposal, [
            'start_date' => '2026-02-10 00:00:00',
        ]);

        $prepared = app(GanttTaskScheduler::class)->prepareForCreate($proposal, $payload);

        $this->assertSame('2026-02-10 00:00:00', $prepared['start_date']);
        $this->assertSame(1, $prepared['sort_order']);
    }

    /** @test */
    public function it_skips_weekends_when_auto_placing_new_tasks()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        // Thu 2026-01-08, duration 3 -> ends Sun 2026-01-11
        $this->createTaskFor($proposal, '2026-01-08', 3, 1);

        $payload = $this->basePayload($proposal);

        $prepared = app(GanttTaskScheduler::class)->prepareForCreate($proposal, $payload);

        // Sun 2026-01-11 + next business day -> Mon 2026-01-12.
        $this->assertSame('2026-01-12 00:00:00', $prepared['start_date']);
    }

    /** @test */
    public function it_ignores_default_sort_order_rows_when_auto_placing()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        // Ordered task: sort_order=1, ends Mon 2026-01-05 + 1 = Tue 2026-01-06.
        $this->createTaskFor($proposal, '2026-01-05', 1, 1);

        // Unordered task (DB default sort_order=0) that ends later in the
        // calendar. The auto-placement must still anchor on the ordered
        // task, not on this "loose" row.
        Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Loose row',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => '2026-02-15 00:00:00',
            'duration' => 5,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 0,
            'hours' => 4,
        ]);

        $payload = $this->basePayload($proposal);

        $prepared = app(GanttTaskScheduler::class)->prepareForCreate($proposal, $payload);

        // Next business day after the ordered task ends (Tue 2026-01-06)
        // is Wed 2026-01-07, not the loose row's end in February.
        $this->assertSame('2026-01-07 00:00:00', $prepared['start_date']);
    }

    /** @test */
    public function get_tasks_endpoint_returns_tasks_ordered_by_sort_order()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $second = $this->createTaskFor($proposal, '2026-01-06', 1, 2);
        $first = $this->createTaskFor($proposal, '2026-01-05', 1, 1);
        $third = $this->createTaskFor($proposal, '2026-01-07', 1, 3);

        $response = $this->getJson(route('proposal.tasks', $proposal));

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $first->id)
            ->assertJsonPath('data.1.id', $second->id)
            ->assertJsonPath('data.2.id', $third->id);
    }

    private function basePayload(Proposal $proposal, array $overrides = []): array
    {
        return array_merge([
            'proposal_id' => $proposal->id,
            'text' => 'Sample task',
            'start_date' => '2026-01-05 00:00:00',
            'hours' => 4,
            'priority_id' => Priority::factory()->create()->id,
            'statu_id' => Statu::factory()->create()->id,
            'progress' => 0,
            'parent' => 0,
        ], $overrides);
    }

    private function createTaskFor(Proposal $proposal, string $startDate, int $duration, int $sortOrder): Task
    {
        return Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Task '.$sortOrder,
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => $startDate.' 00:00:00',
            'duration' => $duration,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => $sortOrder,
            'hours' => 4,
        ]);
    }
}
