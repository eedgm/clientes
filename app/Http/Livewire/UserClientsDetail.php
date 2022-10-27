<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Client;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserClientsDetail extends Component
{
    use AuthorizesRequests;

    public User $user;
    public Client $client;
    public $clientsForSelect = [];
    public $client_id = null;

    public $showingModal = false;
    public $modalTitle = 'New Client';

    protected $rules = [
        'client_id' => ['required', 'exists:clients,id'],
    ];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->clientsForSelect = Client::pluck('name', 'id');
        $this->resetClientData();
    }

    public function resetClientData()
    {
        $this->client = new Client();

        $this->client_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newClient()
    {
        $this->modalTitle = trans('crud.user_clients.new_title');
        $this->resetClientData();

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

        $this->authorize('create', Client::class);

        $this->user->clients()->attach($this->client_id, []);

        $this->hideModal();
    }

    public function detach($client)
    {
        $this->authorize('delete-any', Client::class);

        $this->user->clients()->detach($client);

        $this->resetClientData();
    }

    public function render()
    {
        return view('livewire.user-clients-detail', [
            'userClients' => $this->user
                ->clients()
                ->withPivot([])
                ->paginate(20),
        ]);
    }
}
