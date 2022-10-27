<?php
namespace App\Http\Controllers\Api;

use App\Models\Person;
use App\Models\Version;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonCollection;

class VersionPeopleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Version $version)
    {
        $this->authorize('view', $version);

        $search = $request->get('search', '');

        $people = $version
            ->people()
            ->search($search)
            ->latest()
            ->paginate();

        return new PersonCollection($people);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Version $version, Person $person)
    {
        $this->authorize('update', $version);

        $version->people()->syncWithoutDetaching([$person->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Version $version
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Version $version, Person $person)
    {
        $this->authorize('update', $version);

        $version->people()->detach($person);

        return response()->noContent();
    }
}
