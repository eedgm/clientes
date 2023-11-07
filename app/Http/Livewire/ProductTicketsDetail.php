<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use App\Models\Ticket;
use App\Models\Person;
use Livewire\Component;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Priority;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductTicketsDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Product $product;
    public Ticket $ticket;
    public $statusForSelect = [];
    public $prioritiesForSelect = [];
    public $receiptsForSelect = [];
    public $peopleForSelect = [];
    public $ticketFinishedTicket;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Ticket';

    protected $rules = [
        'ticket.description' => ['required', 'string'],
        'ticket.statu_id' => ['required', 'exists:status,id'],
        'ticket.priority_id' => ['required', 'exists:priorities,id'],
        'ticket.hours' => ['nullable', 'numeric'],
        'ticket.total' => ['nullable', 'numeric'],
        'ticketFinishedTicket' => ['nullable', 'date'],
        'ticket.comments' => ['nullable', 'string'],
        'ticket.person_id' => ['nullable', 'exists:people,id'],
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->statusForSelect = Statu::pluck('name', 'id');
        $this->prioritiesForSelect = Priority::pluck('name', 'id');
        $this->receiptsForSelect = Receipt::pluck('description', 'id');
        $this->peopleForSelect = Person::join('users', 'users.id', '=', 'people.user_id')->where('client_id', $product->client->id)->pluck('users.name', 'people.id');
        $this->resetTicketData();
    }

    public function updated($name, $value)
    {
        if ($name == 'ticket.hours') {
            $this->ticket->total = $value ? $value * $this->product->client->cost_per_hour : '';
        }
    }

    public function resetTicketData()
    {
        $this->ticket = new Ticket();

        $this->ticketFinishedTicket = null;
        $this->ticket->statu_id = 1;
        $this->ticket->priority_id = null;
        $this->ticket->person_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newTicket()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.product_tickets.new_title');
        $this->resetTicketData();

        $this->showModal();
    }

    public function editTicket(Ticket $ticket)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.product_tickets.edit_title');
        $this->ticket = $ticket;

        $this->ticketFinishedTicket = $this->ticket->finished_ticket ? $this->ticket->finished_ticket->format('Y-m-d') : null;

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

        if (!$this->ticket->product_id) {
            $this->authorize('create', Ticket::class);

            $this->ticket->product_id = $this->product->id;
        } else {
            $this->authorize('update', $this->ticket);
        }

        $this->ticket->finished_ticket = $this->ticket->finished_ticket ? \Carbon\Carbon::parse(
            $this->ticketFinishedTicket
        ) : null;

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

        foreach ($this->product->tickets as $ticket) {
            array_push($this->selected, $ticket->id);
        }
    }

    public function render()
    {
        return view('livewire.product-tickets-detail', [
            'tickets' => $this->product->tickets()->paginate(20),
        ]);
    }
}
