<?php

namespace App\Http\Controllers\Api;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PriorityResource;
use App\Http\Resources\PriorityCollection;

class ColorPrioritiesController extends Controller
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

        $priorities = $color
            ->priorities()
            ->search($search)
            ->latest()
            ->paginate();

        return new PriorityCollection($priorities);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Color $color
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Color $color)
    {
        $this->authorize('create', Priority::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
        ]);

        $priority = $color->priorities()->create($validated);

        return new PriorityResource($priority);
    }
}
