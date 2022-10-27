<?php
namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperCollection;

class TaskDevelopersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $search = $request->get('search', '');

        $developers = $task
            ->developers()
            ->search($search)
            ->latest()
            ->paginate();

        return new DeveloperCollection($developers);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task, Developer $developer)
    {
        $this->authorize('update', $task);

        $task->developers()->syncWithoutDetaching([$developer->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Task $task, Developer $developer)
    {
        $this->authorize('update', $task);

        $task->developers()->detach($developer);

        return response()->noContent();
    }
}
