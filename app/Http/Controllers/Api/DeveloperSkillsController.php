<?php
namespace App\Http\Controllers\Api;

use App\Models\Skill;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SkillCollection;

class DeveloperSkillsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Developer $developer)
    {
        $this->authorize('view', $developer);

        $search = $request->get('search', '');

        $skills = $developer
            ->skills()
            ->search($search)
            ->latest()
            ->paginate();

        return new SkillCollection($skills);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Developer $developer, Skill $skill)
    {
        $this->authorize('update', $developer);

        $developer->skills()->syncWithoutDetaching([$skill->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Request $request,
        Developer $developer,
        Skill $skill
    ) {
        $this->authorize('update', $developer);

        $developer->skills()->detach($skill);

        return response()->noContent();
    }
}
