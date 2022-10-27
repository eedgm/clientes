<?php

namespace App\Http\Controllers\Api;

use App\Models\Priority;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCollection;

class PriorityTicketsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Priority $priority)
    {
        $this->authorize('view', $priority);

        $search = $request->get('search', '');

        $tickets = $priority
            ->tickets()
            ->search($search)
            ->latest()
            ->paginate();

        return new TicketCollection($tickets);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Priority $priority
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Priority $priority)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'description' => ['required', 'max:255', 'string'],
            'statu_id' => ['required', 'exists:status,id'],
            'hours' => ['nullable', 'numeric'],
            'finished_ticket' => ['nullable', 'date'],
            'total' => ['nullable', 'numeric'],
            'comments' => ['nullable', 'max:255', 'string'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
            'person_id' => ['nullable', 'exists:people,id'],
        ]);

        $ticket = $priority->tickets()->create($validated);

        return new TicketResource($ticket);
    }
}
