<?php
namespace App\Http\Controllers\Api;

use App\Models\Skill;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperCollection;

class SkillDevelopersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Skill $skill)
    {
        $this->authorize('view', $skill);

        $search = $request->get('search', '');

        $developers = $skill
            ->developers()
            ->search($search)
            ->latest()
            ->paginate();

        return new DeveloperCollection($developers);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Skill $skill, Developer $developer)
    {
        $this->authorize('update', $skill);

        $skill->developers()->syncWithoutDetaching([$developer->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Request $request,
        Skill $skill,
        Developer $developer
    ) {
        $this->authorize('update', $skill);

        $skill->developers()->detach($developer);

        return response()->noContent();
    }
}
