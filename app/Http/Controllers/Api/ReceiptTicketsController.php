<?php

namespace App\Http\Controllers\Api;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCollection;

class ReceiptTicketsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Receipt $receipt)
    {
        $this->authorize('view', $receipt);

        $search = $request->get('search', '');

        $tickets = $receipt
            ->tickets()
            ->search($search)
            ->latest()
            ->paginate();

        return new TicketCollection($tickets);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Receipt $receipt)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'description' => ['required', 'max:255', 'string'],
            'statu_id' => ['required', 'exists:status,id'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'hours' => ['nullable', 'numeric'],
            'finished_ticket' => ['nullable', 'date'],
            'total' => ['nullable', 'numeric'],
            'comments' => ['nullable', 'max:255', 'string'],
            'person_id' => ['nullable', 'exists:people,id'],
        ]);

        $ticket = $receipt->tickets()->create($validated);

        return new TicketResource($ticket);
    }
}
