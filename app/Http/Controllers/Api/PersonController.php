<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PersonCollection;
use App\Http\Requests\PersonStoreRequest;
use App\Http\Requests\PersonUpdateRequest;

class PersonController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Person::class);

        $search = $request->get('search', '');

        $people = Person::search($search)
            ->latest()
            ->paginate();

        return new PersonCollection($people);
    }

    /**
     * @param \App\Http\Requests\PersonStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PersonStoreRequest $request)
    {
        $this->authorize('create', Person::class);

        $validated = $request->validated();
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $person = Person::create($validated);

        return new PersonResource($person);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Person $person)
    {
        $this->authorize('view', $person);

        return new PersonResource($person);
    }

    /**
     * @param \App\Http\Requests\PersonUpdateRequest $request
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
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

        return new PersonResource($person);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Person $person)
    {
        $this->authorize('delete', $person);

        if ($person->photo) {
            Storage::delete($person->photo);
        }

        $person->delete();

        return response()->noContent();
    }
}
