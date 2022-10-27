<?php

namespace App\Http\Controllers\Api;

use App\Models\Statu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCollection;

class StatuTicketsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Statu $statu)
    {
        $this->authorize('view', $statu);

        $search = $request->get('search', '');

        $tickets = $statu
            ->tickets()
            ->search($search)
            ->latest()
            ->paginate();

        return new TicketCollection($tickets);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Statu $statu)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'description' => ['required', 'max:255', 'string'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'hours' => ['nullable', 'numeric'],
            'finished_ticket' => ['nullable', 'date'],
            'total' => ['nullable', 'numeric'],
            'comments' => ['nullable', 'max:255', 'string'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
            'person_id' => ['nullable', 'exists:people,id'],
        ]);

        $ticket = $statu->tickets()->create($validated);

        return new TicketResource($ticket);
    }
}
