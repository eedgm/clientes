<?php

namespace App\Http\Controllers\Api;

use App\Models\Version;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;

class VersionTasksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Version $version)
    {
        $this->authorize('view', $version);

        $search = $request->get('search', '');

        $tasks = $version
            ->tasks()
            ->search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Version $version)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'hours' => ['required', 'numeric'],
            'statu_id' => ['required', 'exists:status,id'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'real_hours' => ['nullable', 'numeric'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ]);

        $task = $version->tasks()->create($validated);

        return new TaskResource($task);
    }
}
