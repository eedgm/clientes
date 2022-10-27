<?php

namespace App\Http\Livewire;

use App\Models\Skill;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeveloperSkillsDetail extends Component
{
    use AuthorizesRequests;

    public Developer $developer;
    public Skill $skill;
    public $skillsForSelect = [];
    public $skill_id = null;
    public $experience_years;
    public $percentage;

    public $showingModal = false;
    public $modalTitle = 'New Skill';

    protected $rules = [
        'skill_id' => ['required', 'exists:skills,id'],
        'experience_years' => ['required', 'max:255'],
        'percentage' => ['nullable', 'numeric'],
    ];

    public function mount(Developer $developer)
    {
        $this->developer = $developer;
        $this->skillsForSelect = Skill::pluck('name', 'id');
        $this->resetSkillData();
    }

    public function resetSkillData()
    {
        $this->skill = new Skill();

        $this->skill_id = null;
        $this->experience_years = null;
        $this->percentage = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newSkill()
    {
        $this->modalTitle = trans('crud.developer_skills.new_title');
        $this->resetSkillData();

        $this->showModal();
    }

    public function showModal()
    {
        $this->resetErrorBag();
        $this->showingModal = true;
    }

    public function hideModal()
    {
        $this->showingModal = false;
    }

    public function save()
    {
        $this->validate();

        $this->authorize('create', Skill::class);

        $this->developer->skills()->attach($this->skill_id, [
            'experience_years' => $this->experience_years,
            'percentage' => $this->percentage,
        ]);

        $this->hideModal();
    }

    public function detach($skill)
    {
        $this->authorize('delete-any', Skill::class);

        $this->developer->skills()->detach($skill);

        $this->resetSkillData();
    }

    public function render()
    {
        return view('livewire.developer-skills-detail', [
            'developerSkills' => $this->developer
                ->skills()
                ->withPivot(['experience_years', 'percentage'])
                ->paginate(20),
        ]);
    }
}
