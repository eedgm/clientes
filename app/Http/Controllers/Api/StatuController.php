<?php

namespace App\Http\Controllers\Api;

use App\Models\Statu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatuResource;
use App\Http\Resources\StatuCollection;
use App\Http\Requests\StatuStoreRequest;
use App\Http\Requests\StatuUpdateRequest;

class StatuController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Statu::class);

        $search = $request->get('search', '');

        $status = Statu::search($search)
            ->latest()
            ->paginate();

        return new StatuCollection($status);
    }

    /**
     * @param \App\Http\Requests\StatuStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatuStoreRequest $request)
    {
        $this->authorize('create', Statu::class);

        $validated = $request->validated();

        $statu = Statu::create($validated);

        return new StatuResource($statu);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Statu $statu)
    {
        $this->authorize('view', $statu);

        return new StatuResource($statu);
    }

    /**
     * @param \App\Http\Requests\StatuUpdateRequest $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function update(StatuUpdateRequest $request, Statu $statu)
    {
        $this->authorize('update', $statu);

        $validated = $request->validated();

        $statu->update($validated);

        return new StatuResource($statu);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Statu $statu)
    {
        $this->authorize('delete', $statu);

        $statu->delete();

        return response()->noContent();
    }
}
