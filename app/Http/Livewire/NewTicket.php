<?php

namespace App\Http\Livewire;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewTicket extends Component
{
    use AuthorizesRequests;

    public Client $client;
    public Product $product;
    public Ticket $ticket;
    public $clientsForSelect = [];
    public $productsForSelect = [];
    public $statusForSelect = [];
    public $prioritiesForSelect = [];
    public $peopleForSelect = [];

    public $showingModal = false;
    public $modalTitle = 'New Ticket';

    protected $rules = [
        'ticket.description' => ['required', 'string'],
        'ticket.statu_id' => ['required', 'exists:status,id'],
        'ticket.priority_id' => ['required', 'exists:priorities,id'],
        'ticket.hours' => ['nullable', 'numeric'],
        'ticket.total' => ['nullable', 'numeric'],
        'ticketFinishedTicket' => ['nullable', 'date'],
        'ticket.comments' => ['nullable', 'max:255', 'string'],
        'ticket.person_id' => ['nullable', 'exists:people,id'],
    ];

    public function mount()
    {
        $this->clientsForSelect = Client::pluck('name', 'id');
    }

    public function render()
    {
        return view('livewire.new-ticket');
    }
}
