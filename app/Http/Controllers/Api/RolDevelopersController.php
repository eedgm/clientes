<?php

namespace App\Http\Controllers\Api;

use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperResource;
use App\Http\Resources\DeveloperCollection;

class RolDevelopersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Rol $rol)
    {
        $this->authorize('view', $rol);

        $search = $request->get('search', '');

        $developers = $rol
            ->developers()
            ->search($search)
            ->latest()
            ->paginate();

        return new DeveloperCollection($developers);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Rol $rol)
    {
        $this->authorize('create', Developer::class);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $developer = $rol->developers()->create($validated);

        return new DeveloperResource($developer);
    }
}
