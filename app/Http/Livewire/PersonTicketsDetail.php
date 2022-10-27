<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use App\Models\Person;
use App\Models\Ticket;
use Livewire\Component;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Priority;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PersonTicketsDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Person $person;
    public Ticket $ticket;
    public $statusForSelect = [];
    public $prioritiesForSelect = [];
    public $productsForSelect = [];
    public $receiptsForSelect = [];
    public $ticketFinishedTicket;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Ticket';

    protected $rules = [
        'ticket.description' => ['required', 'max:255', 'string'],
        'ticket.statu_id' => ['required', 'exists:status,id'],
        'ticket.priority_id' => ['required', 'exists:priorities,id'],
        'ticket.hours' => ['nullable', 'numeric'],
        'ticket.total' => ['nullable', 'numeric'],
        'ticketFinishedTicket' => ['nullable', 'date'],
        'ticket.comments' => ['nullable', 'max:255', 'string'],
        'ticket.product_id' => ['required', 'exists:products,id'],
        'ticket.receipt_id' => ['nullable', 'exists:receipts,id'],
    ];

    public function mount(Person $person)
    {
        $this->person = $person;
        $this->statusForSelect = Statu::pluck('name', 'id');
        $this->prioritiesForSelect = Priority::pluck('name', 'id');
        $this->productsForSelect = Product::pluck('name', 'id');
        $this->receiptsForSelect = Receipt::pluck('description', 'id');
        $this->resetTicketData();
    }

    public function resetTicketData()
    {
        $this->ticket = new Ticket();

        $this->ticketFinishedTicket = null;
        $this->ticket->statu_id = null;
        $this->ticket->priority_id = null;
        $this->ticket->product_id = null;
        $this->ticket->receipt_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newTicket()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.person_tickets.new_title');
        $this->resetTicketData();

        $this->showModal();
    }

    public function editTicket(Ticket $ticket)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.person_tickets.edit_title');
        $this->ticket = $ticket;

        $this->ticketFinishedTicket = $this->ticket->finished_ticket->format(
            'Y-m-d'
        );

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

        if (!$this->ticket->person_id) {
            $this->authorize('create', Ticket::class);

            $this->ticket->person_id = $this->person->id;
        } else {
            $this->authorize('update', $this->ticket);
        }

        $this->ticket->finished_ticket = \Carbon\Carbon::parse(
            $this->ticketFinishedTicket
        );

        $this->ticket->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Ticket::class);

        Ticket::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetTicketData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->person->tickets as $ticket) {
            array_push($this->selected, $ticket->id);
        }
    }

    public function render()
    {
        return view('livewire.person-tickets-detail', [
            'tickets' => $this->person->tickets()->paginate(20),
        ]);
    }
}
