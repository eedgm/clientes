<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IconStoreRequest;
use App\Http\Requests\IconUpdateRequest;
use App\Http\Resources\IconCollection;
use App\Http\Resources\IconResource;
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
            ->paginate();

        return new IconCollection($icons);
    }

    /**
     * @return Response
     */
    public function store(IconStoreRequest $request)
    {
        $this->authorize('create', Icon::class);

        $validated = $request->validated();

        $icon = Icon::create($validated);

        return new IconResource($icon);
    }

    /**
     * @return Response
     */
    public function show(Request $request, Icon $icon)
    {
        $this->authorize('view', $icon);

        return new IconResource($icon);
    }

    /**
     * @return Response
     */
    public function update(IconUpdateRequest $request, Icon $icon)
    {
        $this->authorize('update', $icon);

        $validated = $request->validated();

        $icon->update($validated);

        return new IconResource($icon);
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Icon $icon)
    {
        $this->authorize('delete', $icon);

        $icon->delete();

        return response()->noContent();
    }
}
