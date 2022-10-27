<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Priority;
use Illuminate\Http\Request;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.priorities.index', compact('priorities', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Priority::class);

        $colors = Color::pluck('name', 'id');

        return view('app.priorities.create', compact('colors'));
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

        return redirect()
            ->route('priorities.edit', $priority)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Priority $priority)
    {
        $this->authorize('view', $priority);

        return view('app.priorities.show', compact('priority'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Priority $priority)
    {
        $this->authorize('update', $priority);

        $colors = Color::pluck('name', 'id');

        return view('app.priorities.edit', compact('priority', 'colors'));
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

        return redirect()
            ->route('priorities.edit', $priority)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('priorities.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
