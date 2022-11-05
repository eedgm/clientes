<?php

namespace App\Http\Livewire;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\Payable;
use App\Models\Receipt;
use Livewire\Component;

class ReceiptAssignment extends Component
{
    public $modalTitle = "Agregar pagos";
    public $showingModal = false;
    public Client $client;
    public $payables = null;
    public $tickets = null;
    public $receipt = null;

    public $allSelectedPayables = false;
    public $selectedPayable = [];
    public $allSelectedTickets = false;
    public $selectedTicket = [];

    public function mount(Receipt $receipt)
    {
        $this->showingModal = false;
        $this->client = $receipt->client;
        $this->receipt = $receipt;
    }

    public function newPayment()
    {
        $this->showingModal = true;

    }

    public function toggleFullSelectionPayables()
    {
        if (!$this->allSelectedPayables) {
            $this->selectedPayable = [];
            return;
        }

        foreach ($this->payables as $payable) {
            array_push($this->selectedPayable, $payable->id);
        }
    }

    public function toggleFullSelectionTickets()
    {
        if (!$this->allSelectedTickets) {
            $this->selectedTicket = [];
            return;
        }

        foreach ($this->tickets as $ticket) {
            array_push($this->selectedTicket, $ticket->id);
        }
    }

    public function save() {
        foreach ($this->selectedTicket as $ticket) {
            Ticket::where('id', $ticket)->update(['receipt_id' => $this->receipt->id]);
        }

        foreach ($this->selectedPayable as $payable) {
            Payable::where('id', $payable)->update(['receipt_id' => $this->receipt->id]);
        }

        $this->showingModal = false;

    }

    public function render()
    {
        $products = $this->client->products->modelKeys();
        $this->payables = Payable::whereIn('product_id', $products)
            ->where('date', '<=', now())
            ->where('receipt_id', null)
            ->get();

        $this->tickets = Ticket::whereIn('product_id', $products)
            ->where('finished_ticket', '<=', now())
            ->where('receipt_id', null)
            ->where('statu_id', 6)
            ->get();

        $result = [];
        $payables = Payable::where('receipt_id', $this->receipt->id)->get();
        $tickets = Ticket::where('receipt_id', $this->receipt->id)->get();

        $total = 0;
        $hours = false;
        $person = false;

        foreach ($payables as $payable) {
            $result['payables'][$payable->id]['id'] = $payable->id;
            $result['payables'][$payable->id]['product'] = $payable->product->name;
            $result['payables'][$payable->id]['date'] = $payable->date->format('Y-m-d');
            $result['payables'][$payable->id]['description'] = $payable->name;
            $result['payables'][$payable->id]['hours'] = '-';
            $result['payables'][$payable->id]['person'] = '-';
            $result['payables'][$payable->id]['cost'] = $payable->total;
            $total += $payable->total;
        }

        foreach ($tickets as $ticket) {
            $result['tickets'][$ticket->id]['id'] = $ticket->id;
            $result['tickets'][$ticket->id]['product'] = $ticket->product->name;
            $result['tickets'][$ticket->id]['date'] = $ticket->finished_ticket->format('Y-m-d');
            $result['tickets'][$ticket->id]['description'] = $ticket->description;
            $result['tickets'][$ticket->id]['hours'] = $ticket->hours;
            $result['tickets'][$ticket->id]['person'] = $ticket->person ? $ticket->person->user->name : '-';
            $result['tickets'][$ticket->id]['cost'] = $ticket->total;
            $total += $ticket->total;
            if ($ticket->hours)
                $hours = true;
            if ($ticket->person)
                $person = true;
        }

        return view('livewire.receipt-assignment',
            [
                'payables' => $this->payables,
                'tickets' => $this->tickets,
                'results' => $result,
                'total' => $total,
                'receipt_id' => $this->receipt->id,
                'hours' => $hours,
                'person' => $person,
            ]);
    }

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

        $this->dispatchBrowserEvent('refresh');
    }

    public function removePayable($id)
    {
        Payable::where('id', $id)->update(['receipt_id' => null]);
    }

    public function removeTicket($id)
    {
        Ticket::where('id', $id)->update(['receipt_id' => null]);
    }
}
