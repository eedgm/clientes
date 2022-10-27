<?php

namespace App\Http\Livewire;

use App\Models\Skill;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SkillDevelopersDetail extends Component
{
    use AuthorizesRequests;

    public Skill $skill;
    public Developer $developer;
    public $developersForSelect = [];
    public $developer_id = null;
    public $experience_years;
    public $percentage;

    public $showingModal = false;
    public $modalTitle = 'New Developer';

    protected $rules = [
        'developer_id' => ['required', 'exists:developers,id'],
        'experience_years' => ['required', 'max:255'],
        'percentage' => ['nullable', 'numeric'],
    ];

    public function mount(Skill $skill)
    {
        $this->skill = $skill;
        $this->developersForSelect = Developer::pluck('id', 'id');
        $this->resetDeveloperData();
    }

    public function resetDeveloperData()
    {
        $this->developer = new Developer();

        $this->developer_id = null;
        $this->experience_years = null;
        $this->percentage = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newDeveloper()
    {
        $this->modalTitle = trans('crud.skill_developers.new_title');
        $this->resetDeveloperData();

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

        $this->authorize('create', Developer::class);

        $this->skill->developers()->attach($this->developer_id, [
            'experience_years' => $this->experience_years,
            'percentage' => $this->percentage,
        ]);

        $this->hideModal();
    }

    public function detach($developer)
    {
        $this->authorize('delete-any', Developer::class);

        $this->skill->developers()->detach($developer);

        $this->resetDeveloperData();
    }

    public function render()
    {
        return view('livewire.skill-developers-detail', [
            'skillDevelopers' => $this->skill
                ->developers()
                ->withPivot(['experience_years', 'percentage'])
                ->paginate(20),
        ]);
    }
}
