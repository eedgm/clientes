<?php

namespace App\Http\Controllers\Api;

use App\Models\Priority;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PriorityResource;
use App\Http\Resources\PriorityCollection;
use App\Http\Requests\PriorityStoreRequest;
use App\Http\Requests\PriorityUpdateRequest;

class PriorityController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Priority::class);

        $search = $request->get('search', '');

        $priorities = Priority::search($search)
            ->latest()
            ->paginate();

        return new PriorityCollection($priorities);
    }

    /**
     * @param \App\Http\Requests\PriorityStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PriorityStoreRequest $request)
    {
        $this->authorize('create', Priority::class);

        $validated = $request->validated();

        $priority = Priority::create($validated);

        return new PriorityResource($priority);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Priority $priority)
    {
        $this->authorize('view', $priority);

        return new PriorityResource($priority);
    }

    /**
     * @param \App\Http\Requests\PriorityUpdateRequest $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function update(PriorityUpdateRequest $request, Priority $priority)
    {
        $this->authorize('update', $priority);

        $validated = $request->validated();

        $priority->update($validated);

        return new PriorityResource($priority);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Priority $priority)
    {
        $this->authorize('delete', $priority);

        $priority->delete();

        return response()->noContent();
    }
}
