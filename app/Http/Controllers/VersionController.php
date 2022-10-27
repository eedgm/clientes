<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Version;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.versions.index', compact('versions', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Version::class);

        $proposals = Proposal::pluck('product_name', 'id');
        $users = User::pluck('name', 'id');

        return view('app.versions.create', compact('proposals', 'users'));
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

        return redirect()
            ->route('versions.edit', $version)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Version $version)
    {
        $this->authorize('view', $version);

        return view('app.versions.show', compact('version'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
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

        return redirect()
            ->route('versions.edit', $version)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('versions.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
