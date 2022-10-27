<?php

namespace App\Http\Controllers\Api;

use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Http\Resources\PersonCollection;

class RolPeopleController extends Controller
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

        $people = $rol
            ->people()
            ->search($search)
            ->latest()
            ->paginate();

        return new PersonCollection($people);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rol $rol
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Rol $rol)
    {
        $this->authorize('create', Person::class);

        $validated = $request->validate([
            'photo' => ['nullable', 'file'],
            'description' => ['nullable', 'max:255', 'string'],
            'phone' => ['nullable', 'max:255', 'string'],
            'skype' => ['nullable', 'max:255', 'string'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $person = $rol->people()->create($validated);

        return new PersonResource($person);
    }
}
