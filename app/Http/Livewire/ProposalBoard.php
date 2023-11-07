<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Livewire\Component;
use App\Models\Proposal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProposalBoard extends Component
{
    use AuthorizesRequests;

    public Proposal $proposal;

    public $showingModal = false;
    public $modalTitle = 'New Proposal';
    public $editing = false;

    public $clients;

    protected $rules = [
        'proposal.product_name' => ['required', 'max:255', 'string'],
        'proposal.description' => ['required', 'max:255', 'string'],
        'proposal.client_id' => ['required', 'exists:clients,id'],
    ];

    public function mount()
    {
        $this->clients = Client::pluck('name', 'id');
    }

    public function newProposal()
    {
        $this->proposal = new Proposal();
        $this->editing = false;
        $this->modalTitle = trans('crud.client_proposals.new_title');

        $this->showingModal = true;
    }

    public function save()
    {
        $this->validate();

        if (!$this->proposal->client_id) {
            $this->authorize('create', Proposal::class);

            $this->proposal->client_id = $this->client->id;
        } else {
            $this->authorize('update', $this->proposal);
        }

        $this->proposal->save();

        $this->showingModal = false;
    }

    public function render()
    {
        $proposals = Proposal::get();
        return view('livewire.proposal-board', compact('proposals'));
    }
}
