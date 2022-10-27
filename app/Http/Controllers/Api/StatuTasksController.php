<?php

namespace App\Http\Controllers\Api;

use App\Models\Statu;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;

class StatuTasksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Statu $statu)
    {
        $this->authorize('view', $statu);

        $search = $request->get('search', '');

        $tasks = $statu
            ->tasks()
            ->search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Statu $statu)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'hours' => ['required', 'numeric'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'real_hours' => ['nullable', 'numeric'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ]);

        $task = $statu->tasks()->create($validated);

        return new TaskResource($task);
    }
}
