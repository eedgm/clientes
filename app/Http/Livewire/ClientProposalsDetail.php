<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Livewire\Component;
use App\Models\Proposal;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientProposalsDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Client $client;
    public Proposal $proposal;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Proposal';

    protected $rules = [
        'proposal.product_name' => ['required', 'max:255', 'string'],
        'proposal.description' => ['required', 'max:255', 'string'],
    ];

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->resetProposalData();
    }

    public function resetProposalData()
    {
        $this->proposal = new Proposal();

        $this->dispatchBrowserEvent('refresh');
    }

    public function newProposal()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.client_proposals.new_title');
        $this->resetProposalData();

        $this->showModal();
    }

    public function editProposal(Proposal $proposal)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.client_proposals.edit_title');
        $this->proposal = $proposal;

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

        if (!$this->proposal->client_id) {
            $this->authorize('create', Proposal::class);

            $this->proposal->client_id = $this->client->id;
        } else {
            $this->authorize('update', $this->proposal);
        }

        $this->proposal->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Proposal::class);

        Proposal::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetProposalData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->client->proposals as $proposal) {
            array_push($this->selected, $proposal->id);
        }
    }

    public function render()
    {
        return view('livewire.client-proposals-detail', [
            'proposals' => $this->client->proposals()->paginate(20),
        ]);
    }
}
