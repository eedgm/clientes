<?php

namespace App\Http\Controllers;

use App\Http\Requests\VersionDeveloperCostsRequest;
use App\Http\Requests\VersionStoreRequest;
use App\Http\Requests\VersionUpdateRequest;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class VersionController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Version::class);

        $search = $request->get('search', '');

        $versions = Version::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.versions.index', compact('versions', 'search'));
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Version::class);

        $proposals = Proposal::pluck('product_name', 'id');
        $users = User::pluck('name', 'id');

        return view('app.versions.create', compact('proposals', 'users'));
    }

    /**
     * @return Response
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

        return redirect()
            ->route('versions.edit', $version)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @return Response
     */
    public function show(Request $request, Version $version)
    {
        $this->authorize('view', $version);

        return view('app.versions.show', compact('version'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Version $version)
    {
        $this->authorize('update', $version);

        $proposals = Proposal::pluck('product_name', 'id');
        $users = User::pluck('name', 'id');

        return view(
            'app.versions.edit',
            compact('version', 'proposals', 'users')
        );
    }

    /**
     * @return Response
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

        return redirect()
            ->route('versions.edit', $version)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Version $version)
    {
        $this->authorize('delete', $version);

        if ($version->attachment) {
            Storage::delete($version->attachment);
        }

        $version->delete();

        return redirect()
            ->route('versions.index')
            ->withSuccess(__('crud.common.removed'));
    }

    /**
     * Sync per-developer cost overrides for this version.
     *
     * Payload shape: { overrides: [{ developer_id, cost_per_hour }] }
     * Existing rows for developers that are missing from the payload
     * are detached so the calculator falls back to the developer's
     * base cost.
     */
    public function updateDeveloperCosts(
        VersionDeveloperCostsRequest $request,
        Version $version
    ) {
        $this->authorize('update', $version);

        $overrides = collect($request->validated()['overrides'] ?? [])
            ->keyBy('developer_id')
            ->all();

        $sync = [];

        foreach ($overrides as $developerId => $override) {
            $cost = $override['cost_per_hour'] ?? null;

            $sync[(int) $developerId] = $cost === null
                ? ['cost_per_hour' => null]
                : ['cost_per_hour' => (float) $cost];
        }

        $version->developers()->sync($sync);

        return response()->json([
            'action' => 'updated',
            'overrides' => $version->developers()
                ->get(['developers.id', 'developers.user_id'])
                ->map(function ($developer) use ($version) {
                    $pivot = $version->developers()
                        ->where('developers.id', $developer->id)
                        ->first()
                        ?->pivot;

                    return [
                        'developer_id' => $developer->id,
                        'cost_per_hour' => $pivot?->cost_per_hour,
                    ];
                })
                ->values(),
        ]);
    }
}
