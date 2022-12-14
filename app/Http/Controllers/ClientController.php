<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;

class ClientController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $this->authorize('view-any', Client::class);

        $search = $request->get('search', '');

        $clients = Client::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.clients.index', compact('clients', 'search'));
    }

    public function dashboard(Request $request) {
        return view('app.clients.dashboard');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Client::class);

        return view('app.clients.create');
    }

    /**
     * @param \App\Http\Requests\ClientStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientStoreRequest $request)
    {
        $this->authorize('create', Client::class);

        $validated = $request->validated();
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('public');
        }

        $client = Client::create($validated);

        return redirect()
            ->route('clients.edit', $client)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Client $client)
    {
        $this->authorize('view', $client);

        return view('app.clients.show', compact('client'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        return view('app.clients.edit', compact('client'));
    }

    /**
     * @param \App\Http\Requests\ClientUpdateRequest $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(ClientUpdateRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $validated = $request->validated();
        if ($request->hasFile('logo')) {
            if ($client->logo) {
                Storage::delete($client->logo);
            }

            $validated['logo'] = $request->file('logo')->store('public');
        }

        $client->update($validated);

        return redirect()
            ->route('clients.edit', $client)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Client $client)
    {
        $this->authorize('delete', $client);

        if ($client->logo) {
            Storage::delete($client->logo);
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
