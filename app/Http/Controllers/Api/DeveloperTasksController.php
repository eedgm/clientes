<?php
namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;

class DeveloperTasksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Developer $developer)
    {
        $this->authorize('view', $developer);

        $search = $request->get('search', '');

        $tasks = $developer
            ->tasks()
            ->search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Developer $developer, Task $task)
    {
        $this->authorize('update', $developer);

        $developer->tasks()->syncWithoutDetaching([$task->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Developer $developer, Task $task)
    {
        $this->authorize('update', $developer);

        $developer->tasks()->detach($task);

        return response()->noContent();
    }
}
