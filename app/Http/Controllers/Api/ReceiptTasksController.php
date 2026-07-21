<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceiptTasksController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request, Receipt $receipt)
    {
        $this->authorize('view', $receipt);

        $search = $request->get('search', '');

        $tasks = $receipt
            ->tasks()
            ->search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    /**
     * @return Response
     */
    public function store(Request $request, Receipt $receipt)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'hours' => ['required', 'numeric'],
            'statu_id' => ['required', 'exists:status,id'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'real_hours' => ['nullable', 'numeric'],
        ]);

        $task = $receipt->tasks()->create($validated);

        return new TaskResource($task);
    }
}
