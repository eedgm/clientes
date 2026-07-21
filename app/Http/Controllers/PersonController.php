<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonStoreRequest;
use App\Http\Requests\PersonUpdateRequest;
use App\Models\Client;
use App\Models\Person;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Person::class);

        $search = $request->get('search', '');

        $people = Person::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.people.index', compact('people', 'search'));
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Person::class);

        $clients = Client::pluck('name', 'id');
        $rols = Rol::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('app.people.create', compact('clients', 'rols', 'users'));
    }

    /**
     * @return Response
     */
    public function store(PersonStoreRequest $request)
    {
        $this->authorize('create', Person::class);

        $validated = $request->validated();
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $person = Person::create($validated);

        return redirect()
            ->route('people.edit', $person)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @return Response
     */
    public function show(Request $request, Person $person)
    {
        $this->authorize('view', $person);

        return view('app.people.show', compact('person'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $clients = Client::pluck('name', 'id');
        $rols = Rol::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view(
            'app.people.edit',
            compact('person', 'clients', 'rols', 'users')
        );
    }

    /**
     * @return Response
     */
    public function update(PersonUpdateRequest $request, Person $person)
    {
        $this->authorize('update', $person);

        $validated = $request->validated();
        if ($request->hasFile('photo')) {
            if ($person->photo) {
                Storage::delete($person->photo);
            }

            $validated['photo'] = $request->file('photo')->store('public');
        }

        $person->update($validated);

        return redirect()
            ->route('people.edit', $person)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Person $person)
    {
        $this->authorize('delete', $person);

        if ($person->photo) {
            Storage::delete($person->photo);
        }

        $person->delete();

        return redirect()
            ->route('people.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
