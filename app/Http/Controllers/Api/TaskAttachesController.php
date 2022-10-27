<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttachResource;
use App\Http\Resources\AttachCollection;

class TaskAttachesController extends Controller
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

        $attaches = $task
            ->attaches()
            ->search($search)
            ->latest()
            ->paginate();

        return new AttachCollection($attaches);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('create', Attach::class);

        $validated = $request->validate([
            'attachment' => ['file', 'max:1024', 'required'],
            'description' => ['nullable', 'max:255', 'string'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attach = $task->attaches()->create($validated);

        return new AttachResource($attach);
    }
}
