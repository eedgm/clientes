<?php

namespace App\Http\Controllers\Api;

use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Resources\RolResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\RolCollection;
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
            ->paginate();

        return new RolCollection($rols);
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

        return new RolResource($rol);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Rol $rol)
    {
        $this->authorize('view', $rol);

        return new RolResource($rol);
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

        return new RolResource($rol);
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

        return response()->noContent();
    }
}
