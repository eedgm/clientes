<?php

namespace App\Http\Controllers\Api;

use App\Models\Priority;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;

class PriorityTasksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Priority $priority)
    {
        $this->authorize('view', $priority);

        $search = $request->get('search', '');

        $tasks = $priority
            ->tasks()
            ->search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Priority $priority)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'hours' => ['required', 'numeric'],
            'statu_id' => ['required', 'exists:status,id'],
            'real_hours' => ['nullable', 'numeric'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ]);

        $task = $priority->tasks()->create($validated);

        return new TaskResource($task);
    }
}
