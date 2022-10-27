<?php
namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCollection;

class DeveloperTicketsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Developer $developer
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
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
