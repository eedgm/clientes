<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconStoreRequest;
use App\Http\Requests\IconUpdateRequest;
use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IconController extends Controller
{
    /**
     * @return Response
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
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Icon::class);

        return view('app.icons.create');
    }

    /**
     * @return Response
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
     * @return Response
     */
    public function show(Request $request, Icon $icon)
    {
        $this->authorize('view', $icon);

        return view('app.icons.show', compact('icon'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Icon $icon)
    {
        $this->authorize('update', $icon);

        return view('app.icons.edit', compact('icon'));
    }

    /**
     * @return Response
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
     * @return Response
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
