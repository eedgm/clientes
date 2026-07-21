<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColorStoreRequest;
use App\Http\Requests\ColorUpdateRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ColorController extends Controller
{
    /**
     * @return Response
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
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Color::class);

        return view('app.colors.create');
    }

    /**
     * @return Response
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
     * @return Response
     */
    public function show(Request $request, Color $color)
    {
        $this->authorize('view', $color);

        return view('app.colors.show', compact('color'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Color $color)
    {
        $this->authorize('update', $color);

        return view('app.colors.edit', compact('color'));
    }

    /**
     * @return Response
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
     * @return Response
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
