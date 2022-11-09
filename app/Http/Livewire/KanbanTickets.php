<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use App\Models\Ticket;
use Livewire\Component;

class KanbanTickets extends Component
{
    public $showingModal = false;

    public $modalTitle = 'New Ticket';

    public function mount() {
        $this->showingModal = false;
    }

    public function addTicket(Statu $status)
    {
        $this->showingModal = true;
    }

    public function render()
    {
        $status = Statu::all();
        $tickets = Ticket::whereNull('receipt_id')->get();

        return view('livewire.dashboard.kanban-tickets', compact('status', 'tickets'));
    }
}
