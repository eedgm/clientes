<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Product;
use Livewire\Component;
use App\Models\Priority;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewTicket extends Component
{
    use AuthorizesRequests;

    public $clients;
    public $products = null;
    public Ticket $ticket;
    public $statusForSelect = [];
    public $prioritiesForSelect = [];
    public $peopleForSelect = [];

    public $ticket_client_id;

    public $editing = false;
    public $showingModal = false;
    public $modalTitle = 'New Ticket';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $rules = [
        'ticket_client_id' => ['required', 'exists:clients,id'],
        'ticket.product_id' => ['required', 'exists:products,id'],
        'ticket.description' => ['required', 'string'],
        'ticket.statu_id' => ['required', 'exists:status,id'],
        'ticket.priority_id' => ['required', 'exists:priorities,id'],
        'ticket.hours' => ['nullable', 'numeric'],
        'ticket.total' => ['nullable', 'numeric'],
        'ticket.comments' => ['nullable', 'max:255', 'string'],
    ];

    public function mount()
    {
        $this->statusForSelect = Statu::pluck('name', 'id');
        $this->prioritiesForSelect = Priority::pluck('name', 'id');
        $this->showingModal = false;
        $this->clients = Client::pluck('name', 'id');
    }

    public function createTicket()
    {
        $this->ticket = new Ticket();
        $this->ticket->statu_id = 1;
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
    }

    public function save()
    {
        $this->validate();

        $this->authorize('create', Ticket::class);

        $this->ticket->save();

        $this->emit('refreshComponent');

        $this->ticket_client_id = null;
        $this->products = [];

        $this->showingModal = false;
    }

    public function render()
    {
        return view('livewire.new-ticket');
    }
}
