<?php

namespace App\Http\Livewire;

use App\Models\Receipt;
use Livewire\Component;

class ReceiptsDashboard extends Component
{
    public $total = 0;
    public function render()
    {
        $receipts = Receipt::where('charged', false)->get();
        return view('livewire.dashboard/receipts-dashboard', compact('receipts'));
    }
}
