<?php

namespace App\Http\Controllers\Api;

use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperResource;
use App\Http\Resources\DeveloperCollection;
use App\Http\Requests\DeveloperStoreRequest;
use App\Http\Requests\DeveloperUpdateRequest;

class DeveloperController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Developer::class);

        $search = $request->get('search', '');

        $developers = Developer::search($search)
            ->latest()
            ->paginate();

        return new DeveloperCollection($developers);
    }

    /**
     * @param \App\Http\Requests\DeveloperStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeveloperStoreRequest $request)
    {
        $this->authorize('create', Developer::class);

        $validated = $request->validated();

        $developer = Developer::create($validated);

        return new DeveloperResource($developer);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Developer $developer)
    {
        $this->authorize('view', $developer);

        return new DeveloperResource($developer);
    }

    /**
     * @param \App\Http\Requests\DeveloperUpdateRequest $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function update(
        DeveloperUpdateRequest $request,
        Developer $developer
    ) {
        $this->authorize('update', $developer);

        $validated = $request->validated();

        $developer->update($validated);

        return new DeveloperResource($developer);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Developer $developer)
    {
        $this->authorize('delete', $developer);

        $developer->delete();

        return response()->noContent();
    }
}
