<?php

namespace App\Http\Controllers\Api;

use App\Models\Version;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VersionResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\VersionCollection;
use App\Http\Requests\VersionStoreRequest;
use App\Http\Requests\VersionUpdateRequest;

class VersionController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Version::class);

        $search = $request->get('search', '');

        $versions = Version::search($search)
            ->latest()
            ->paginate();

        return new VersionCollection($versions);
    }

    /**
     * @param \App\Http\Requests\VersionStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(VersionStoreRequest $request)
    {
        $this->authorize('create', Version::class);

        $validated = $request->validated();
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $version = Version::create($validated);

        return new VersionResource($version);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Version $version)
    {
        $this->authorize('view', $version);

        return new VersionResource($version);
    }

    /**
     * @param \App\Http\Requests\VersionUpdateRequest $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function update(VersionUpdateRequest $request, Version $version)
    {
        $this->authorize('update', $version);

        $validated = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($version->attachment) {
                Storage::delete($version->attachment);
            }

            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $version->update($validated);

        return new VersionResource($version);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Version $version)
    {
        $this->authorize('delete', $version);

        if ($version->attachment) {
            Storage::delete($version->attachment);
        }

        $version->delete();

        return response()->noContent();
    }
}
