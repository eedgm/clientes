<?php

namespace App\Http\Livewire;

use App\Models\Person;
use Livewire\Component;
use App\Models\Version;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VersionPeopleDetail extends Component
{
    use AuthorizesRequests;

    public Version $version;
    public Person $person;
    public $peopleForSelect = [];
    public $person_id = null;
    public $comments;

    public $showingModal = false;
    public $modalTitle = 'New Person';

    protected $rules = [
        'person_id' => ['required', 'exists:people,id'],
        'comments' => ['required', 'max:255', 'string'],
    ];

    public function mount(Version $version)
    {
        $this->version = $version;
        $this->peopleForSelect = Person::pluck('description', 'id');
        $this->resetPersonData();
    }

    public function resetPersonData()
    {
        $this->person = new Person();

        $this->person_id = null;
        $this->comments = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newPerson()
    {
        $this->modalTitle = trans('crud.version_people.new_title');
        $this->resetPersonData();

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

        $this->authorize('create', Person::class);

        $this->version->people()->attach($this->person_id, [
            'comments' => $this->comments,
        ]);

        $this->hideModal();
    }

    public function detach($person)
    {
        $this->authorize('delete-any', Person::class);

        $this->version->people()->detach($person);

        $this->resetPersonData();
    }

    public function render()
    {
        return view('livewire.version-people-detail', [
            'versionPeople' => $this->version
                ->people()
                ->withPivot(['comments'])
                ->paginate(20),
        ]);
    }
}
