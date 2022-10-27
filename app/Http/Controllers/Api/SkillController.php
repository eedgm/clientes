<?php

namespace App\Http\Controllers\Api;

use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Http\Resources\SkillCollection;
use App\Http\Requests\SkillStoreRequest;
use App\Http\Requests\SkillUpdateRequest;

class SkillController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Skill::class);

        $search = $request->get('search', '');

        $skills = Skill::search($search)
            ->latest()
            ->paginate();

        return new SkillCollection($skills);
    }

    /**
     * @param \App\Http\Requests\SkillStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SkillStoreRequest $request)
    {
        $this->authorize('create', Skill::class);

        $validated = $request->validated();

        $skill = Skill::create($validated);

        return new SkillResource($skill);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Skill $skill)
    {
        $this->authorize('view', $skill);

        return new SkillResource($skill);
    }

    /**
     * @param \App\Http\Requests\SkillUpdateRequest $request
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function update(SkillUpdateRequest $request, Skill $skill)
    {
        $this->authorize('update', $skill);

        $validated = $request->validated();

        $skill->update($validated);

        return new SkillResource($skill);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Skill $skill)
    {
        $this->authorize('delete', $skill);

        $skill->delete();

        return response()->noContent();
    }
}
