<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Http\Resources\PersonCollection;

class ClientPeopleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Client $client)
    {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $people = $client
            ->people()
            ->search($search)
            ->latest()
            ->paginate();

        return new PersonCollection($people);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Client $client)
    {
        $this->authorize('create', Person::class);

        $validated = $request->validate([
            'photo' => ['nullable', 'file'],
            'description' => ['nullable', 'max:255', 'string'],
            'phone' => ['nullable', 'max:255', 'string'],
            'skype' => ['nullable', 'max:255', 'string'],
            'rol_id' => ['required', 'exists:rols,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $person = $client->people()->create($validated);

        return new PersonResource($person);
    }
}
