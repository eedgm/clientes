<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonStoreRequest;
use App\Http\Requests\PersonUpdateRequest;
use App\Http\Resources\PersonCollection;
use App\Http\Resources\PersonResource;
use App\Models\Person;
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
            ->paginate();

        return new PersonCollection($people);
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

        return new PersonResource($person);
    }

    /**
     * @return Response
     */
    public function show(Request $request, Person $person)
    {
        $this->authorize('view', $person);

        return new PersonResource($person);
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

        return new PersonResource($person);
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

        return response()->noContent();
    }
}
