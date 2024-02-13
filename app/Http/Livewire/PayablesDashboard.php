<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Payable;
use Livewire\Component;

class PayablesDashboard extends Component
{
    public function render()
    {
        $total = 0;
        $payables = Payable::where('receipt_id', null)->where('date', '<=', Carbon::now()->addMonth(5))->get();
        return view('livewire.dashboard/payables-dashboard', compact('payables', 'total'));
    }
}
