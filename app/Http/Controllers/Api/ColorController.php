<?php

namespace App\Http\Controllers\Api;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ColorResource;
use App\Http\Resources\ColorCollection;
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
            ->paginate();

        return new ColorCollection($colors);
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

        return new ColorResource($color);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Color $color)
    {
        $this->authorize('view', $color);

        return new ColorResource($color);
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

        return new ColorResource($color);
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

        return response()->noContent();
    }
}
