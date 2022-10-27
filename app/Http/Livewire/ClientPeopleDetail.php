<?php

namespace App\Http\Livewire;

use App\Models\Rol;
use App\Models\User;
use App\Models\Client;
use App\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientPeopleDetail extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesRequests;

    public Client $client;
    public Person $person;
    public User $user;
    public $password = null;
    public $rolsForSelect = [];
    public $usersForSelect = [];
    public $personPhoto;
    public $uploadIteration = 0;
    public $user_id = null;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;
    public $userExist = false;

    public $modalTitle = 'New Person';

    protected $rules = [
        'user.name' => ['required', 'max:255', 'string'],
        'password' => ['nullable', 'max:255', 'string'],
        'user.email' => ['required', 'max:255', 'string'],
        'personPhoto' => ['nullable', 'file'],
        'person.description' => ['nullable', 'max:255', 'string'],
        'person.phone' => ['nullable', 'max:255', 'string'],
        'person.skype' => ['nullable', 'max:255', 'string'],
        'person.rol_id' => ['required', 'exists:rols,id'],
    ];

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->rolsForSelect = Rol::pluck('name', 'id');
        $this->usersForSelect = User::pluck('name', 'id');
        $this->userExist = false;
        $this->userInAnotherClient = false;
        $this->resetPersonData();
    }

    public function resetPersonData()
    {
        $this->person = new Person();
        $this->user = new User();

        $this->personPhoto = null;
        $this->person->rol_id = null;
        $this->person->user_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newPerson()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.client_people.new_title');
        $this->resetPersonData();

        $this->showModal();
    }

    public function editPerson(Person $person)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.client_people.edit_title');
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

        if (!$this->person->client_id) {
            $this->authorize('create', Person::class);

            $this->user->password = Hash::make($this->password);

            $this->user->save();

            $this->person->client_id = $this->client->id;

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

    public function updated($name, $value)
    {
        if ($name == 'user.email') {
            if (User::where('email', $value)->exists()) {
                $this->userExist = true;
                $this->user->email = '';
            }
            else {
                $this->userExist = false;
            }
        }
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

        foreach ($this->client->people as $person) {
            array_push($this->selected, $person->id);
        }
    }

    public function render()
    {
        return view('livewire.client-people-detail', [
            'people' => $this->client->people()->paginate(20),
        ]);
    }
}
