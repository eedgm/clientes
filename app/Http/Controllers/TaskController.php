<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Statu;
use App\Models\Receipt;
use App\Models\Version;
use App\Models\Priority;
use App\Models\Proposal;
use Illuminate\Http\Request;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;

class TaskController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
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

    public function addGanttTask(Request $request) {
        $task = new Task();

        $task->proposal_id = $request->proposal_id;
        $task->statu_id = 1;
        $task->priority_id = $request->priority_id;
        $task->hours = $request->hours;
        $task->text = $request->text;
        $task->start_date = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;

        $task->save();

        return response()->json([
            "action"=> "inserted",
            "tid" => $task->id
        ]);
    }

    public function updateGanttTask(Request $request, Task $task){
        $task->text = $request->text;
        $task->priority_id = $request->priority_id;
        $task->hours = $request->hours;
        $task->start_date = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;

        $task->save();

        return response()->json([
            "action"=> "updated"
        ]);
    }

    public function destroyGanttTask(Task $task){
        $task->delete();

        return response()->json([
            "action"=> "deleted"
        ]);
    }

    public function getTasks(Request $request, Proposal $proposal)
    {
        $tasks = $proposal->tasks;

        return response()->json([
            "data" => $tasks,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
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
     * @param \App\Http\Requests\TaskStoreRequest $request
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        return view('app.tasks.show', compact('task'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
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
     * @param \App\Http\Requests\TaskUpdateRequest $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
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
