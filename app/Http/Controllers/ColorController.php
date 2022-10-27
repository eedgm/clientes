<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Requests\ColorStoreRequest;
use App\Http\Requests\ColorUpdateRequest;

class ColorController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Color::class);

        $search = $request->get('search', '');

        $colors = Color::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.colors.index', compact('colors', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Color::class);

        return view('app.colors.create');
    }

    /**
     * @param \App\Http\Requests\ColorStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ColorStoreRequest $request)
    {
        $this->authorize('create', Color::class);

        $validated = $request->validated();

        $color = Color::create($validated);

        return redirect()
            ->route('colors.edit', $color)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Color $color)
    {
        $this->authorize('view', $color);

        return view('app.colors.show', compact('color'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Color $color)
    {
        $this->authorize('update', $color);

        return view('app.colors.edit', compact('color'));
    }

    /**
     * @param \App\Http\Requests\ColorUpdateRequest $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function update(ColorUpdateRequest $request, Color $color)
    {
        $this->authorize('update', $color);

        $validated = $request->validated();

        $color->update($validated);

        return redirect()
            ->route('colors.edit', $color)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Color $color)
    {
        $this->authorize('delete', $color);

        $color->delete();

        return redirect()
            ->route('colors.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
