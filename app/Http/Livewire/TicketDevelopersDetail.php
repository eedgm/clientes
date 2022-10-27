<?php

namespace App\Http\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketDevelopersDetail extends Component
{
    use AuthorizesRequests;

    public Ticket $ticket;
    public Developer $developer;
    public $developersForSelect = [];
    public $developer_id = null;
    public $assigments;
    public $comments;
    public $gain;

    public $showingModal = false;
    public $modalTitle = 'New Developer';

    protected $rules = [
        'developer_id' => ['required', 'exists:developers,id'],
        'assigments' => ['nullable', 'max:255', 'string'],
        'comments' => ['nullable', 'max:255', 'string'],
        'gain' => ['nullable', 'numeric'],
    ];

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->developersForSelect = Developer::pluck('id', 'id');
        $this->resetDeveloperData();
    }

    public function resetDeveloperData()
    {
        $this->developer = new Developer();

        $this->developer_id = null;
        $this->assigments = null;
        $this->comments = null;
        $this->gain = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newDeveloper()
    {
        $this->modalTitle = trans('crud.ticket_developers.new_title');
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

        $this->ticket->developers()->attach($this->developer_id, [
            'assigments' => $this->assigments,
            'comments' => $this->comments,
            'gain' => $this->gain,
        ]);

        $this->hideModal();
    }

    public function detach($developer)
    {
        $this->authorize('delete-any', Developer::class);

        $this->ticket->developers()->detach($developer);

        $this->resetDeveloperData();
    }

    public function render()
    {
        return view('livewire.ticket-developers-detail', [
            'ticketDevelopers' => $this->ticket
                ->developers()
                ->withPivot(['assigments', 'comments', 'gain'])
                ->paginate(20),
        ]);
    }
}
