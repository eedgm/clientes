<?php

namespace App\Http\Livewire;

use App\Models\Rol;
use App\Models\User;
use Livewire\Component;
use App\Models\Developer;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserDevelopersDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public User $user;
    public Developer $developer;
    public $rolsForSelect = [];

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Developer';

    protected $rules = [
        'developer.rol_id' => ['required', 'exists:rols,id'],
    ];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->rolsForSelect = Rol::pluck('name', 'id');
        $this->resetDeveloperData();
    }

    public function resetDeveloperData()
    {
        $this->developer = new Developer();

        $this->developer->rol_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newDeveloper()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.user_developers.new_title');
        $this->resetDeveloperData();

        $this->showModal();
    }

    public function editDeveloper(Developer $developer)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.user_developers.edit_title');
        $this->developer = $developer;

        $this->dispatchBrowserEvent('refresh');

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

        if (!$this->developer->user_id) {
            $this->authorize('create', Developer::class);

            $this->developer->user_id = $this->user->id;
        } else {
            $this->authorize('update', $this->developer);
        }

        $this->developer->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Developer::class);

        Developer::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetDeveloperData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->user->developers as $developer) {
            array_push($this->selected, $developer->id);
        }
    }

    public function render()
    {
        return view('livewire.user-developers-detail', [
            'developers' => $this->user->developers()->paginate(20),
        ]);
    }
}
