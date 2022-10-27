<?php

namespace App\Http\Controllers\Api;

use App\Models\Icon;
use Illuminate\Http\Request;
use App\Http\Resources\IconResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\IconCollection;
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
            ->paginate();

        return new IconCollection($icons);
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

        return new IconResource($icon);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Icon $icon)
    {
        $this->authorize('view', $icon);

        return new IconResource($icon);
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

        return new IconResource($icon);
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

        return response()->noContent();
    }
}
