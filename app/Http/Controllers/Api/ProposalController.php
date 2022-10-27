<?php

namespace App\Http\Controllers\Api;

use App\Models\Proposal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProposalResource;
use App\Http\Resources\ProposalCollection;
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
            ->paginate();

        return new ProposalCollection($proposals);
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

        return new ProposalResource($proposal);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        return new ProposalResource($proposal);
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

        return new ProposalResource($proposal);
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

        return response()->noContent();
    }
}
