<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.skills.index', compact('skills', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Skill::class);

        return view('app.skills.create');
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

        return redirect()
            ->route('skills.edit', $skill)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Skill $skill)
    {
        $this->authorize('view', $skill);

        return view('app.skills.show', compact('skill'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Skill $skill
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Skill $skill)
    {
        $this->authorize('update', $skill);

        return view('app.skills.edit', compact('skill'));
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

        return redirect()
            ->route('skills.edit', $skill)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('skills.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
