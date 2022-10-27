<?php

namespace App\Http\Controllers\Api;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatuResource;
use App\Http\Resources\StatuCollection;

class ColorStatusController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Color $color)
    {
        $this->authorize('view', $color);

        $search = $request->get('search', '');

        $status = $color
            ->status()
            ->search($search)
            ->latest()
            ->paginate();

        return new StatuCollection($status);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Color $color)
    {
        $this->authorize('create', Statu::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'limit' => ['required', 'numeric'],
            'icon_id' => ['required', 'exists:icons,id'],
        ]);

        $statu = $color->status()->create($validated);

        return new StatuResource($statu);
    }
}
