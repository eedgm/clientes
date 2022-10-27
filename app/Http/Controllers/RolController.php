<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Requests\RolStoreRequest;
use App\Http\Requests\RolUpdateRequest;

class RolController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Rol::class);

        $search = $request->get('search', '');

        $rols = Rol::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.rols.index', compact('rols', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Rol::class);

        return view('app.rols.create');
    }

    /**
     * @param \App\Http\Requests\RolStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RolStoreRequest $request)
    {
        $this->authorize('create', Rol::class);

        $validated = $request->validated();

        $rol = Rol::create($validated);

        return redirect()
            ->route('rols.edit', $rol)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Rol $rol)
    {
        $this->authorize('view', $rol);

        return view('app.rols.show', compact('rol'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Rol $rol)
    {
        $this->authorize('update', $rol);

        return view('app.rols.edit', compact('rol'));
    }

    /**
     * @param \App\Http\Requests\RolUpdateRequest $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function update(RolUpdateRequest $request, Rol $rol)
    {
        $this->authorize('update', $rol);

        $validated = $request->validated();

        $rol->update($validated);

        return redirect()
            ->route('rols.edit', $rol)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Rol $rol)
    {
        $this->authorize('delete', $rol);

        $rol->delete();

        return redirect()
            ->route('rols.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
