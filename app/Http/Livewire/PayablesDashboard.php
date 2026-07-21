<?php

namespace App\Http\Livewire;

use App\Models\Payable;
use Carbon\Carbon;
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
