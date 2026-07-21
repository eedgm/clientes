<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Ticket::class);

        $search = $request->get('search', '');

        $tickets = Ticket::search($search)
            ->latest()
            ->paginate();

        return new TicketCollection($tickets);
    }

    /**
     * @return Response
     */
    public function store(TicketStoreRequest $request)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validated();

        $ticket = Ticket::create($validated);

        return new TicketResource($ticket);
    }

    /**
     * @return Response
     */
    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return new TicketResource($ticket);
    }

    /**
     * @return Response
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validated();

        $ticket->update($validated);

        return new TicketResource($ticket);
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->noContent();
    }
}
