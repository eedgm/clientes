<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Models\Person;
use App\Models\Priority;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Statu;
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
            ->paginate(10)
            ->withQueryString();

        return view('app.tickets.index', compact('tickets', 'search'));
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $status = Statu::pluck('name', 'id');
        $priorities = Priority::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $receipts = Receipt::pluck('number', 'id');
        $people = Person::join('users', 'users.id', '=', 'people.user_id')->pluck('users.name', 'people.id');

        return view(
            'app.tickets.create',
            compact('status', 'priorities', 'products', 'receipts', 'people')
        );
    }

    /**
     * @return Response
     */
    public function store(TicketStoreRequest $request)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validated();

        $ticket = Ticket::create($validated);

        return redirect()
            ->route('tickets.edit', $ticket)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @return Response
     */
    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return view('app.tickets.show', compact('ticket'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $status = Statu::pluck('name', 'id');
        $priorities = Priority::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $receipts = Receipt::pluck('description', 'id');
        $people = Person::join('users', 'users.id', '=', 'people.user_id')->where('client_id', $ticket->product->client->id)->pluck('users.name', 'people.id');

        return view(
            'app.tickets.edit',
            compact(
                'ticket',
                'status',
                'priorities',
                'products',
                'receipts',
                'people'
            )
        );
    }

    /**
     * @return Response
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validated();

        $ticket->update($validated);

        return redirect()
            ->route('tickets.edit', $ticket)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
