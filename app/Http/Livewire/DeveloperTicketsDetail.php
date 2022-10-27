<?php

namespace App\Http\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeveloperTicketsDetail extends Component
{
    use AuthorizesRequests;

    public Developer $developer;
    public Ticket $ticket;
    public $ticketsForSelect = [];
    public $ticket_id = null;
    public $assigments;
    public $comments;
    public $gain;

    public $showingModal = false;
    public $modalTitle = 'New Ticket';

    protected $rules = [
        'ticket_id' => ['required', 'exists:tickets,id'],
        'assigments' => ['nullable', 'max:255', 'string'],
        'comments' => ['nullable', 'max:255', 'string'],
        'gain' => ['nullable', 'numeric'],
    ];

    public function mount(Developer $developer)
    {
        $this->developer = $developer;
        $this->ticketsForSelect = Ticket::pluck('description', 'id');
        $this->resetTicketData();
    }

    public function resetTicketData()
    {
        $this->ticket = new Ticket();

        $this->ticket_id = null;
        $this->assigments = null;
        $this->comments = null;
        $this->gain = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newTicket()
    {
        $this->modalTitle = trans('crud.developer_tickets.new_title');
        $this->resetTicketData();

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

        $this->authorize('create', Ticket::class);

        $this->developer->tickets()->attach($this->ticket_id, [
            'assigments' => $this->assigments,
            'comments' => $this->comments,
            'gain' => $this->gain,
        ]);

        $this->hideModal();
    }

    public function detach($ticket)
    {
        $this->authorize('delete-any', Ticket::class);

        $this->developer->tickets()->detach($ticket);

        $this->resetTicketData();
    }

    public function render()
    {
        return view('livewire.developer-tickets-detail', [
            'developerTickets' => $this->developer
                ->tickets()
                ->withPivot(['assigments', 'comments', 'gain'])
                ->paginate(20),
        ]);
    }
}
