<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Http\Request;
use App\Http\Requests\ProposalStoreRequest;
use App\Http\Requests\ProposalUpdateRequest;

class ProposalController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Proposal::class);

        $search = $request->get('search', '');

        $proposals = Proposal::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.proposals.index', compact('proposals', 'search'));
    }

    public function gantt(Request $request, Proposal $proposal)
    {
        return view('app.proposals.gantt', compact('proposal'));
    }

    public function board(Request $request)
    {
        $proposals = Proposal::get();
        return view('app.proposals.board', compact('proposals'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Proposal::class);

        $clients = Client::pluck('name', 'id');

        return view('app.proposals.create', compact('clients'));
    }

    /**
     * @param \App\Http\Requests\ProposalStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProposalStoreRequest $request)
    {
        $this->authorize('create', Proposal::class);

        $validated = $request->validated();

        $proposal = Proposal::create($validated);

        return redirect()
            ->route('proposals.edit', $proposal)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        return view('app.proposals.show', compact('proposal'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        $clients = Client::pluck('name', 'id');

        return view('app.proposals.edit', compact('proposal', 'clients'));
    }

    /**
     * @param \App\Http\Requests\ProposalUpdateRequest $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function update(ProposalUpdateRequest $request, Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        $validated = $request->validated();

        $proposal->update($validated);

        return redirect()
            ->route('proposals.edit', $proposal)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Proposal $proposal)
    {
        $this->authorize('delete', $proposal);

        $proposal->delete();

        return redirect()
            ->route('proposals.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
