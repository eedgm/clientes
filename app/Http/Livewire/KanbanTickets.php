<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Product;
use Livewire\Component;
use App\Models\Priority;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KanbanTickets extends Component
{
    use AuthorizesRequests;

    public $showingModal = false;
    public $clients;
    public $products = null;
    public $newStatus;
    public $moveTicket;
    public $colors = [];
    public $icons = [];
    public $ticket_client_id;
    public $ticketFinishedTicket;
    public $status;

    public $ticketStatusSelected = null;

    public $editing = false;
    public $modalTitle = 'New Ticket';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $rules = [
        'ticket_client_id' => ['required', 'exists:clients,id'],
        'ticket.product_id' => ['required', 'exists:products,id'],
        'ticket.description' => ['required', 'string'],
        'ticket.statu_id' => ['required', 'exists:status,id'],
        'ticket.priority_id' => ['required', 'exists:priorities,id'],
        'ticketFinishedTicket' => ['nullable', 'date'],
        'ticket.hours' => ['nullable', 'numeric'],
        'ticket.total' => ['nullable', 'numeric'],
        'ticket.progress' => ['nullable', 'numeric'],
        'ticket.comments' => ['nullable', 'max:255', 'string'],
    ];

    public function mount() {
        $this->showingModal = false;
        $this->statusForSelect = Statu::pluck('name', 'id');
        $this->prioritiesForSelect = Priority::pluck('name', 'id');
        $this->clients = Client::pluck('name', 'id');
        $this->ticketFinishedTicket = null;
        $this->status = Statu::all();
        $this->colors = [
            1 => 'bg-blue-100',
            2 => 'bg-green-100',
            3 => 'bg-yellow-100',
            4 => 'bg-red-100',
            5 => 'bg-purple-100',
            6 => 'bg-sky-100'
        ];

        $this->icons = [
            1 => 'bx-file-blank text-blue-500',
            2 => 'bx-key text-green-500',
            3 => 'bx-alarm-off text-yellow-500',
            4 => 'bx-layer-minus text-red-500',
            5 => 'bx-bell text-purple-500',
            6 => 'bx-dollar text-sky-500'
        ];
    }

    public function addTicket(Statu $status)
    {
        $this->ticket = new Ticket();
        $this->ticket->statu_id = $status->id;
        $this->ticket->progress = 0;
        $this->showingModal = true;
    }

    public function selectProducts()
    {
        $this->products = Product::where('client_id', $this->ticket_client_id)->pluck('name', 'id');
    }

    public function updated($name, $value)
    {
        if ($name == 'ticket.hours') {
            $product = Product::where('id', $this->ticket->product_id)->first();
            $this->ticket->total = $value ? $value * $product->client->cost_per_hour : '';
        }

        if ($name == 'ticket.statu_id') {
            $this->ticketStatusSelected = $this->ticket->statu_id;
        }
    }

    public function edit(Ticket $ticket)
    {
        $this->editing = true;
        $this->ticket = $ticket;
        $this->ticket->progress = $this->ticket->progress ?? 0;
        $this->ticketStatusSelected = $this->ticket->statu_id;
        $this->ticket_client_id = $this->ticket->product->client->id;
        $this->products = Product::where('client_id', $this->ticket_client_id)->pluck('name', 'id');
        $this->ticketFinishedTicket = $this->ticket->finished_ticket ? $this->ticket->finished_ticket->format('Y-m-d') : null;
        $this->showingModal = true;
    }

    public function save()
    {
        $this->validate();

        $this->authorize('create', Ticket::class);

        if ($this->editing) {
            $this->ticket->finished_ticket = \Carbon\Carbon::parse(
                $this->ticketFinishedTicket
            );
        }

        $this->ticket->save();

        $this->ticket_client_id = null;
        $this->products = [];

        $this->showingModal = false;
    }

    public function delete(Ticket $ticket)
    {
        $this->authorize('delete-any', Ticket::class);

        $ticket->delete();
    }

    public function onDragEnter($event, $status)
    {
        $this->newStatus = $status;
    }

    public function onDragEnd($event, Ticket $ticket)
    {
        $ticket->statu_id = $this->newStatus;
        $ticket->save();
    }

    public function render()
    {
        $tickets = Ticket::whereNull('receipt_id')->get();

        return view('livewire.dashboard.kanban-tickets', compact('tickets'));
    }
}
