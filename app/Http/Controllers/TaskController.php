<?php

namespace App\Http\Controllers;

use App\Http\Requests\GanttBulkTaskRequest;
use App\Http\Requests\GanttTaskDevelopersRequest;
use App\Http\Requests\GanttTaskStoreRequest;
use App\Http\Requests\GanttTaskUpdateRequest;
use App\Http\Requests\ProposalTaskReorderRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Developer;
use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Receipt;
use App\Models\Statu;
use App\Models\Task;
use App\Models\Version;
use App\Services\GanttTaskScheduler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Task::class);

        $search = $request->get('search', '');

        $tasks = Task::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.tasks.index', compact('tasks', 'search'));
    }

    public function addGanttTask(GanttTaskStoreRequest $request, GanttTaskScheduler $scheduler)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();
        $validated['progress'] = $validated['progress'] ?? 0;
        $validated['parent'] = $validated['parent'] ?? 0;
        $validated['hours'] = $validated['hours'] ?? 0;

        $proposal = Proposal::findOrFail($validated['proposal_id']);
        $validated = $scheduler->prepareForCreate($proposal, $validated);

        $task = Task::create($validated);

        $scheduler->cascadeSchedule($task);

        $proposal->load('tasks');

        return response()->json([
            'action' => 'inserted',
            'tid' => $task->id,
            'task' => $this->serializeTask($task),
            'tasks' => $this->serializeTasks($proposal->tasks),
        ], 201);
    }

    public function updateGanttTask(
        GanttTaskUpdateRequest $request,
        Task $task,
        GanttTaskScheduler $scheduler
    ) {
        $this->authorize('update', $task);

        $validated = $request->validated();

        unset($validated['proposal_id']);

        $validated = $scheduler->prepareForUpdate($task->proposal, $task, $validated);

        $task->update($validated);

        $scheduler->cascadeSchedule($task->fresh());

        $task->proposal->load('tasks');

        return response()->json([
            'action' => 'updated',
            'task' => $this->serializeTask($task->fresh()),
            'tasks' => $this->serializeTasks($task->proposal->tasks),
        ]);
    }

    public function reorderProposalTasks(
        ProposalTaskReorderRequest $request,
        Proposal $proposal,
        GanttTaskScheduler $scheduler
    ) {
        $this->authorize('update', $proposal);

        $orderedIds = $request->validated()['ordered_ids'] ?? [];

        $scheduler->applyOrdering($proposal, $orderedIds);

        $proposal->load('tasks');

        return response()->json([
            'action' => 'reordered',
            'count' => $proposal->tasks->count(),
            'tasks' => $this->serializeTasks($proposal->tasks),
        ]);
    }

    /**
     * Sync the developer assignments for a gantt task and recompute
     * the task's total hours from the sum of pivot hours.
     */
    public function syncGanttDevelopers(
        GanttTaskDevelopersRequest $request,
        Task $task
    ) {
        $this->authorize('update', $task);

        $assignments = $request->validated()['developers'] ?? [];

        $this->syncTaskDeveloperAssignments($task, $assignments);

        return response()->json([
            'action' => 'updated',
            'hours' => (float) $task->fresh()->effective_hours,
        ]);
    }

    public function destroyGanttTask(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'action' => 'deleted',
        ]);
    }

    public function getTasks(Request $request, Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        $tasks = $proposal->orderedTasks()->with('developers.user')->get();

        return response()->json([
            'data' => $tasks,
        ]);
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Task::class);

        $status = Statu::pluck('name', 'id');
        $priorities = Priority::pluck('name', 'id');
        $versions = Version::pluck('attachment', 'id');
        $receipts = Receipt::pluck('description', 'id');

        return view(
            'app.tasks.create',
            compact('status', 'priorities', 'versions', 'receipts')
        );
    }

    /**
     * @return Response
     */
    public function store(TaskStoreRequest $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();

        $task = Task::create($validated);

        return redirect()
            ->route('tasks.edit', $task)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @return Response
     */
    public function show(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        return view('app.tasks.show', compact('task'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $status = Statu::pluck('name', 'id');
        $priorities = Priority::pluck('name', 'id');
        $versions = Version::pluck('attachment', 'id');
        $receipts = Receipt::pluck('description', 'id');

        return view(
            'app.tasks.edit',
            compact('task', 'status', 'priorities', 'versions', 'receipts')
        );
    }

    /**
     * @return Response
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $task->update($validated);

        return redirect()
            ->route('tasks.edit', $task)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->withSuccess(__('crud.common.removed'));
    }

    /**
     * Preview bulk task creation from human-readable JSON.
     * Resolves all references (priority, status, developers) and returns
     * a preview with resolution status so the user can verify before storing.
     * The effective_hours field reflects what the server will persist
     * after developer assignment recalculation.
     */
    public function bulkPreviewGanttTasks(
        GanttBulkTaskRequest $request,
        Proposal $proposal
    ) {
        $this->authorize('view', $proposal);

        $data = $request->validated();

        $issues = [];
        $previewTasks = [];

        foreach ($data['tasks'] as $i => $taskInput) {
            $taskPreview = [
                'index' => $i,
                'text' => $taskInput['text'],
                'hours' => (float) $taskInput['hours'],
                'effective_hours' => null,
                'priority' => null,
                'status' => null,
                'developers' => [],
            ];

            // Resolve priority
            $priority = Priority::whereRaw('LOWER(name) = ?', [strtolower($taskInput['priority'])])->first();
            if ($priority) {
                $taskPreview['priority'] = [
                    'name' => $priority->name,
                    'id' => $priority->id,
                    'resolved' => true,
                ];
            } else {
                $taskPreview['priority'] = [
                    'name' => $taskInput['priority'],
                    'id' => null,
                    'resolved' => false,
                ];
                $issues[] = 'Task #'.($i + 1).": Unknown priority \"{$taskInput['priority']}\"";
            }

            // Resolve status
            $status = Statu::whereRaw('LOWER(name) = ?', [strtolower($taskInput['status'])])->first();
            if ($status) {
                $taskPreview['status'] = [
                    'name' => $status->name,
                    'id' => $status->id,
                    'resolved' => true,
                ];
            } else {
                $taskPreview['status'] = [
                    'name' => $taskInput['status'],
                    'id' => null,
                    'resolved' => false,
                ];
                $issues[] = 'Task #'.($i + 1).": Unknown status \"{$taskInput['status']}\"";
            }

            // Resolve developers
            $developerHoursSum = 0;
            $hasDeveloperHours = false;

            foreach ($taskInput['developers'] ?? [] as $j => $devInput) {
                $devPreview = [
                    'name' => $devInput['name'] ?? ($devInput['email'] ?? 'Unknown'),
                    'email' => $devInput['email'] ?? null,
                    'hours' => isset($devInput['hours']) ? (float) $devInput['hours'] : null,
                    'developer_id' => null,
                    'resolved' => false,
                    'ambiguous' => false,
                ];

                $resolution = $this->resolveDeveloperByNameOrEmail(
                    $devInput['name'] ?? '',
                    $devInput['email'] ?? null
                );

                if ($resolution['ambiguous']) {
                    $label = $devInput['email'] ?? $devInput['name'] ?? 'Unknown';
                    $issues[] = 'Task #'.($i + 1).": Developer \"{$label}\" is ambiguous — specify email";
                    $devPreview['ambiguous'] = true;
                } elseif ($resolution['developer']) {
                    $devPreview['developer_id'] = $resolution['developer']->id;
                    $devPreview['resolved'] = true;
                    $devPreview['name'] = $resolution['developer']->user->name;

                    if ($devPreview['hours'] !== null) {
                        $hasDeveloperHours = true;
                        $developerHoursSum += $devPreview['hours'];
                    }
                } else {
                    $label = $devInput['email'] ?? $devInput['name'] ?? 'Unknown';
                    $issues[] = 'Task #'.($i + 1).": Unknown developer \"{$label}\"";
                }

                $taskPreview['developers'][] = $devPreview;
            }

            // Compute effective hours: sum of developer hours when present,
            // otherwise the raw hours from the JSON. This matches what
            // syncTaskDeveloperAssignments does on store.
            $taskPreview['effective_hours'] = $hasDeveloperHours
                ? $developerHoursSum
                : (float) $taskInput['hours'];

            $previewTasks[] = $taskPreview;
        }

        return response()->json([
            'action' => 'preview',
            'total_tasks' => count($data['tasks']),
            'issues' => $issues,
            'has_issues' => count($issues) > 0,
            'preview' => $previewTasks,
        ]);
    }

    /**
     * Bulk store tasks from human-readable JSON.
     * Resolves all references, creates tasks within a DB transaction,
     * assigns developers, and returns the authoritative task list.
     */
    public function bulkStoreGanttTasks(
        GanttBulkTaskRequest $request,
        Proposal $proposal,
        GanttTaskScheduler $scheduler
    ) {
        $this->authorize('update', $proposal);

        $data = $request->validated();

        // Resolve and fail fast on any unknown or ambiguous reference
        $resolvedTasks = [];
        $errors = [];

        foreach ($data['tasks'] as $i => $taskInput) {
            $priority = Priority::whereRaw('LOWER(name) = ?', [strtolower($taskInput['priority'])])->first();
            if (! $priority) {
                $errors["tasks.{$i}.priority"] = "Unknown priority: {$taskInput['priority']}";
            }

            $status = Statu::whereRaw('LOWER(name) = ?', [strtolower($taskInput['status'])])->first();
            if (! $status) {
                $errors["tasks.{$i}.status"] = "Unknown status: {$taskInput['status']}";
            }

            $developers = [];
            foreach ($taskInput['developers'] ?? [] as $j => $devInput) {
                $resolution = $this->resolveDeveloperByNameOrEmail(
                    $devInput['name'] ?? '',
                    $devInput['email'] ?? null
                );

                if ($resolution['ambiguous']) {
                    $label = $devInput['email'] ?? $devInput['name'] ?? 'Unknown';
                    $errors["tasks.{$i}.developers.{$j}.name"] = "Ambiguous developer \"{$label}\", specify email";
                } elseif ($resolution['developer']) {
                    $developers[] = [
                        'developer_id' => $resolution['developer']->id,
                        'hours' => $devInput['hours'] ?? null,
                    ];
                } else {
                    $label = $devInput['email'] ?? $devInput['name'] ?? 'Unknown';
                    $errors["tasks.{$i}.developers.{$j}.name"] = "Unknown developer: {$label}";
                }
            }

            // Only collect resolved data when both priority and status
            // were found; errors are collected separately above.
            if ($priority && $status) {
                $resolvedTasks[] = [
                    'text' => $taskInput['text'],
                    'hours' => (float) $taskInput['hours'],
                    'priority_id' => $priority->id,
                    'statu_id' => $status->id,
                    'developers' => $developers,
                ];
            }
        }

        if (count($errors) > 0) {
            throw ValidationException::withMessages($errors);
        }

        // Create tasks within a transaction
        $createdTasks = DB::transaction(function () use ($proposal, $scheduler, $resolvedTasks) {
            $tasks = [];

            foreach ($resolvedTasks as $resolved) {
                $developers = $resolved['developers'];
                unset($resolved['developers']);

                $payload = array_merge($resolved, [
                    'progress' => 0,
                    'parent' => 0,
                    'start_date' => now()->format('Y-m-d H:i:s'),
                ]);

                $payload = $scheduler->prepareForCreate($proposal, $payload);

                $task = Task::create($payload);

                if (count($developers) > 0) {
                    $this->syncTaskDeveloperAssignments($task, $developers);
                }

                $scheduler->cascadeSchedule($task);

                $tasks[] = $task;
            }

            return $tasks;
        });

        $proposal->load('tasks');

        return response()->json([
            'action' => 'bulk_stored',
            'count' => count($createdTasks),
            'tasks' => $this->serializeTasks($proposal->tasks),
        ], 201);
    }

    /**
     * Reconcile a task's developer pivot with the incoming payload
     * and recompute the task total hours. Legacy tasks without
     * assignment hours keep their previous `tasks.hours` value.
     */
    private function syncTaskDeveloperAssignments(Task $task, array $assignments): void
    {
        $sync = [];

        foreach ($assignments as $assignment) {
            if (! isset($assignment['developer_id'])) {
                continue;
            }

            $hours = $assignment['hours'] ?? null;

            $sync[(int) $assignment['developer_id']] = $hours === null
                ? []
                : ['hours' => (float) $hours];
        }

        $task->developers()->sync($sync);

        $total = $task->assignment_hours_total;

        if ($total !== null) {
            $task->forceFill(['hours' => $total])->save();
        }
    }

    /**
     * Serialize a single task to the authoritative shape the gantt
     * needs to keep its timeline and order in sync after a server
     * mutation (create/update/reorder).
     *
     * @return array<string, mixed>
     */
    private function serializeTask(Task $task): array
    {
        return [
            'id' => $task->id,
            'text' => $task->text,
            'start_date' => $task->start_date?->format('Y-m-d H:i:s'),
            'duration' => (int) $task->duration,
            'sort_order' => (int) $task->sort_order,
            'progress' => $task->progress !== null ? (float) $task->progress : 0.0,
            'parent' => (int) $task->parent,
            'priority_id' => $task->priority_id,
            'statu_id' => $task->statu_id,
            'proposal_id' => $task->proposal_id,
        ];
    }

    /**
     * Serialize a collection of tasks in persisted (sort_order, id)
     * order so the frontend can replace local rows with the
     * authoritative server state without a full page refresh.
     *
     * @param  Collection<int, Task>  $tasks
     * @return array<int, array<string, mixed>>
     */
    private function serializeTasks(Collection $tasks): array
    {
        return $tasks
            ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
            ->values()
            ->map(fn (Task $task) => $this->serializeTask($task))
            ->all();
    }

    /**
     * Resolve a developer by email first if provided, otherwise by
     * user name. Returns a tuple of [developer|null, ambiguous-bool].
     *
     * When an email is provided it is authoritative — if no developer
     * matches that email, the result is "not found" with NO fallback
     * to name resolution.  When only a name is provided and multiple
     * developers share the same user name, the result is marked
     * ambiguous so the caller can reject with a clear message.
     *
     * @return array{developer: Developer|null, ambiguous: bool}
     */
    private function resolveDeveloperByNameOrEmail(string $name, ?string $email): array
    {
        if ($email !== null) {
            $developer = Developer::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->first();

            if ($developer) {
                return ['developer' => $developer, 'ambiguous' => false];
            }

            // Email is authoritative — no fallback to name.
            return ['developer' => null, 'ambiguous' => false];
        }

        $matches = Developer::whereHas('user', function ($query) use ($name) {
            $query->whereRaw('LOWER(name) = ?', [strtolower($name)]);
        })->get();

        if ($matches->count() === 1) {
            return ['developer' => $matches->first(), 'ambiguous' => false];
        }

        if ($matches->count() > 1) {
            return ['developer' => null, 'ambiguous' => true];
        }

        return ['developer' => null, 'ambiguous' => false];
    }
}
