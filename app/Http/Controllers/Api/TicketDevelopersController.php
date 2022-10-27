<?php
namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeveloperCollection;

class TicketDevelopersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $search = $request->get('search', '');

        $developers = $ticket
            ->developers()
            ->search($search)
            ->latest()
            ->paginate();

        return new DeveloperCollection($developers);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function store(
        Request $request,
        Ticket $ticket,
        Developer $developer
    ) {
        $this->authorize('update', $ticket);

        $ticket->developers()->syncWithoutDetaching([$developer->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @param \App\Models\Developer $developer
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Request $request,
        Ticket $ticket,
        Developer $developer
    ) {
        $this->authorize('update', $ticket);

        $ticket->developers()->detach($developer);

        return response()->noContent();
    }
}
