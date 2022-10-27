<?php

namespace App\Http\Livewire;

use App\Models\Rol;
use App\Models\User;
use App\Models\Person;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserPeopleDetail extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesRequests;

    public User $user;
    public Person $person;
    public $clientsForSelect = [];
    public $rolsForSelect = [];
    public $personPhoto;
    public $uploadIteration = 0;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Person';

    protected $rules = [
        'personPhoto' => ['nullable', 'file'],
        'person.description' => ['nullable', 'max:255', 'string'],
        'person.phone' => ['nullable', 'max:255', 'string'],
        'person.skype' => ['nullable', 'max:255', 'string'],
        'person.client_id' => ['required', 'exists:clients,id'],
        'person.rol_id' => ['required', 'exists:rols,id'],
    ];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->clientsForSelect = Client::pluck('name', 'id');
        $this->rolsForSelect = Rol::pluck('name', 'id');
        $this->resetPersonData();
    }

    public function resetPersonData()
    {
        $this->person = new Person();

        $this->personPhoto = null;
        $this->person->client_id = null;
        $this->person->rol_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newPerson()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.user_people.new_title');
        $this->resetPersonData();

        $this->showModal();
    }

    public function editPerson(Person $person)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.user_people.edit_title');
        $this->person = $person;

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

        if (!$this->person->user_id) {
            $this->authorize('create', Person::class);

            $this->person->user_id = $this->user->id;
        } else {
            $this->authorize('update', $this->person);
        }

        if ($this->personPhoto) {
            $this->person->photo = $this->personPhoto->store('public');
        }

        $this->person->save();

        $this->uploadIteration++;

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Person::class);

        collect($this->selected)->each(function (string $id) {
            $person = Person::findOrFail($id);

            if ($person->photo) {
                Storage::delete($person->photo);
            }

            $person->delete();
        });

        $this->selected = [];
        $this->allSelected = false;

        $this->resetPersonData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->user->people as $person) {
            array_push($this->selected, $person->id);
        }
    }

    public function render()
    {
        return view('livewire.user-people-detail', [
            'people' => $this->user->people()->paginate(20),
        ]);
    }
}
