<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use App\Models\Ticket;
use Livewire\Component;

class TicketsCompleteDashboard extends Component
{
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updateData($name, $id, $value) {
        $ticket = Ticket::where('id', $id)->first();
        if ($name == 'hours' && $value > 0) {
            $client_cost = $ticket->product->client->cost_per_hour;
            $total = $value * $client_cost;
            $ticket->hours = $value;
            $ticket->total = $total;
            $ticket->update();
        }
        if ($name == 'total' && $value > 0) {
            $ticket->total = $value;
            $ticket->hours = $value / $ticket->product->client->cost_per_hour;
            $ticket->update();
        }
    }

    public function completed(Ticket $ticket, $value)
    {
        $ticket->finished_ticket = \Carbon\Carbon::parse(
            $value
        );
        $ticket->update();
    }

    public function changeStatus($status, $id) {
        $ticket = Ticket::where('id', $id)->first();
        $ticket->statu_id = $status;
        $ticket->update();

        $this->emit('refreshComponent');
    }

    public function render()
    {
        $tickets = Ticket::where('statu_id', 6)->where('receipt_id', null)->get();

        $all_status = Statu::pluck('name', 'id');

        return view('livewire.dashboard/tickets-complete-dashboard', compact('tickets', 'all_status'));
    }
}
