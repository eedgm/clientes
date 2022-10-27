<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Http\Resources\PersonCollection;

class UserPeopleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $people = $user
            ->people()
            ->search($search)
            ->latest()
            ->paginate();

        return new PersonCollection($people);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->authorize('create', Person::class);

        $validated = $request->validate([
            'photo' => ['nullable', 'file'],
            'description' => ['nullable', 'max:255', 'string'],
            'phone' => ['nullable', 'max:255', 'string'],
            'skype' => ['nullable', 'max:255', 'string'],
            'rol_id' => ['required', 'exists:rols,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $person = $user->people()->create($validated);

        return new PersonResource($person);
    }
}
