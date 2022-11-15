<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Ticket;
use App\Models\Receipt;
use Livewire\Component;

class IncomesTotal extends Component
{

    public function render()
    {
        $start = new Carbon('first day of last month');
        $end = new Carbon('last day of last month');

        $start1 = new Carbon('first day of this month');
        $end1 = new Carbon('last day of this month');

        $receipts_last_month_income = Receipt::where('charged', true)
            ->where('real_date', '>=', $start->toDateString())
            ->where('real_date', '<=', $end->toDateString())
            ->get();

        $receipts_this_month_income = Receipt::where('charged', true)
            ->where('real_date', '>=', $start1->toDateString())
            ->where('real_date', '<=', $end1->toDateString())
            ->get();

        $tickets_completed_without_receipt = Ticket::whereNull('receipt_id')
            ->where('statu_id', 6)
            ->get();

        $last_month_income = 0;
        foreach ($receipts_last_month_income as $receipt) {
            $last_month_income += ($receipt->tickets->sum('total')) + ($receipt->payables->sum('total'));
        }

        $this_month_income = 0;
        foreach ($receipts_this_month_income as $receipt) {
            $this_month_income += ($receipt->tickets->sum('total')) + ($receipt->payables->sum('total'));
        }

        $tickets_total = 0;
        foreach ($tickets_completed_without_receipt as $ticket) {
            $tickets_total += $ticket->total;
        }

        return view('livewire.incomes-total', compact('last_month_income', 'this_month_income', 'tickets_total'));
    }
}
