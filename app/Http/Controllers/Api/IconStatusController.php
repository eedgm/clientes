<?php

namespace App\Http\Controllers\Api;

use App\Models\Icon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatuResource;
use App\Http\Resources\StatuCollection;

class IconStatusController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Icon $icon)
    {
        $this->authorize('view', $icon);

        $search = $request->get('search', '');

        $status = $icon
            ->status()
            ->search($search)
            ->latest()
            ->paginate();

        return new StatuCollection($status);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Icon $icon
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Icon $icon)
    {
        $this->authorize('create', Statu::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'limit' => ['required', 'numeric'],
            'color_id' => ['required', 'exists:colors,id'],
        ]);

        $statu = $icon->status()->create($validated);

        return new StatuResource($statu);
    }
}
