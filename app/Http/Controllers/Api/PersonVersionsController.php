<?php
namespace App\Http\Controllers\Api;

use App\Models\Person;
use App\Models\Version;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VersionCollection;

class PersonVersionsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Person $person)
    {
        $this->authorize('view', $person);

        $search = $request->get('search', '');

        $versions = $person
            ->versions()
            ->search($search)
            ->latest()
            ->paginate();

        return new VersionCollection($versions);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Person $person
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Person $person, Version $version)
    {
        $this->authorize('update', $person);

        $person->versions()->syncWithoutDetaching([$version->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Person $person
     * @param \App\Models\Version $version
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Person $person, Version $version)
    {
        $this->authorize('update', $person);

        $person->versions()->detach($version);

        return response()->noContent();
    }
}
