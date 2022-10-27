<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use Illuminate\Http\Request;
use App\Http\Requests\IconStoreRequest;
use App\Http\Requests\IconUpdateRequest;

class IconController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Icon::class);

        $search = $request->get('search', '');

        $icons = Icon::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.icons.index', compact('icons', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Icon::class);

        return view('app.icons.create');
    }

    /**
     * @param \App\Http\Requests\IconStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(IconStoreRequest $request)
    {
        $this->authorize('create', Icon::class);

        $validated = $request->validated();

        $icon = Icon::create($validated);

        return redirect()
            ->route('icons.edit', $icon)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Icon $icon)
    {
        $this->authorize('view', $icon);

        return view('app.icons.show', compact('icon'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Icon $icon)
    {
        $this->authorize('update', $icon);

        return view('app.icons.edit', compact('icon'));
    }

    /**
     * @param \App\Http\Requests\IconUpdateRequest $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function update(IconUpdateRequest $request, Icon $icon)
    {
        $this->authorize('update', $icon);

        $validated = $request->validated();

        $icon->update($validated);

        return redirect()
            ->route('icons.edit', $icon)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Icon $icon)
    {
        $this->authorize('delete', $icon);

        $icon->delete();

        return redirect()
            ->route('icons.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
