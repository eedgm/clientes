<?php

namespace App\Http\Controllers;

use App\Http\Requests\GanttTaskStoreRequest;
use App\Http\Requests\GanttTaskUpdateRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Receipt;
use App\Models\Statu;
use App\Models\Task;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function addGanttTask(GanttTaskStoreRequest $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();
        $validated['progress'] = $validated['progress'] ?? 0;
        $validated['parent'] = $validated['parent'] ?? 0;

        $task = Task::create($validated);

        return response()->json([
            'action' => 'inserted',
            'tid' => $task->id,
        ], 201);
    }

    public function updateGanttTask(GanttTaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        unset($validated['proposal_id']);

        $task->update($validated);

        return response()->json([
            'action' => 'updated',
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

        $tasks = $proposal->tasks()->get();

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
}
