<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolStoreRequest;
use App\Http\Requests\RolUpdateRequest;
use App\Http\Resources\RolCollection;
use App\Http\Resources\RolResource;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolController extends Controller
{
    /**
     * @return Response
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
     * @return Response
     */
    public function store(RolStoreRequest $request)
    {
        $this->authorize('create', Rol::class);

        $validated = $request->validated();

        $rol = Rol::create($validated);

        return new RolResource($rol);
    }

    /**
     * @return Response
     */
    public function show(Request $request, Rol $rol)
    {
        $this->authorize('view', $rol);

        return new RolResource($rol);
    }

    /**
     * @return Response
     */
    public function update(RolUpdateRequest $request, Rol $rol)
    {
        $this->authorize('update', $rol);

        $validated = $request->validated();

        $rol->update($validated);

        return new RolResource($rol);
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Rol $rol)
    {
        $this->authorize('delete', $rol);

        $rol->delete();

        return response()->noContent();
    }
}
