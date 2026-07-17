<?php

namespace App\Http\Controllers;

use App\Http\Requests\GanttTaskDevelopersRequest;
use App\Http\Requests\GanttTaskStoreRequest;
use App\Http\Requests\GanttTaskUpdateRequest;
use App\Http\Requests\ProposalTaskReorderRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
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
}
