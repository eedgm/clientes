<?php

namespace App\Http\Controllers;

use App\Models\Statu;
use App\Models\Ticket;
use App\Models\Person;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Priority;
use Illuminate\Http\Request;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;

class TicketController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
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
     * @param \App\Http\Requests\TicketStoreRequest $request
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return view('app.tickets.show', compact('ticket'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
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
     * @param \App\Http\Requests\TicketUpdateRequest $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
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
