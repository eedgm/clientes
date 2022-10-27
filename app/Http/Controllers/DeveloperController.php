<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use App\Models\Developer;
use Illuminate\Http\Request;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.developers.index', compact('developers', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Developer::class);

        $users = User::pluck('name', 'id');
        $rols = Rol::pluck('name', 'id');

        return view('app.developers.create', compact('users', 'rols'));
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

        return redirect()
            ->route('developers.edit', $developer)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Developer $developer)
    {
        $this->authorize('view', $developer);

        return view('app.developers.show', compact('developer'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Developer $developer)
    {
        $this->authorize('update', $developer);

        $users = User::pluck('name', 'id');
        $rols = Rol::pluck('name', 'id');

        return view(
            'app.developers.edit',
            compact('developer', 'users', 'rols')
        );
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

        return redirect()
            ->route('developers.edit', $developer)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('developers.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
