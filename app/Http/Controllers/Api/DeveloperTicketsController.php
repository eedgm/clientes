<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCollection;
use App\Models\Developer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeveloperTicketsController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request, Developer $developer)
    {
        $this->authorize('view', $developer);

        $search = $request->get('search', '');

        $tickets = $developer
            ->tickets()
            ->search($search)
            ->latest()
            ->paginate();

        return new TicketCollection($tickets);
    }

    /**
     * @return Response
     */
    public function store(
        Request $request,
        Developer $developer,
        Ticket $ticket
    ) {
        $this->authorize('update', $developer);

        $developer->tickets()->syncWithoutDetaching([$ticket->id]);

        return response()->noContent();
    }

    /**
     * @return Response
     */
    public function destroy(
        Request $request,
        Developer $developer,
        Ticket $ticket
    ) {
        $this->authorize('update', $developer);

        $developer->tickets()->detach($ticket);

        return response()->noContent();
    }
}
