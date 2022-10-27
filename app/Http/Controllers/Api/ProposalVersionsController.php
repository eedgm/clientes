<?php

namespace App\Http\Controllers\Api;

use App\Models\Proposal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VersionResource;
use App\Http\Resources\VersionCollection;

class ProposalVersionsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        $search = $request->get('search', '');

        $versions = $proposal
            ->versions()
            ->search($search)
            ->latest()
            ->paginate();

        return new VersionCollection($versions);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Proposal $proposal)
    {
        $this->authorize('create', Version::class);

        $validated = $request->validate([
            'attachment' => ['file', 'max:1024', 'nullable'],
            'user_id' => ['required', 'exists:users,id'],
            'total' => ['required', 'numeric'],
            'time' => ['required', 'date'],
            'cost_per_hour' => ['required', 'numeric'],
            'hour_per_day' => ['required', 'numeric'],
            'months_to_pay' => ['required', 'numeric'],
            'unexpected' => ['required', 'numeric'],
            'company_gain' => ['required', 'numeric'],
            'bank_tax' => ['required', 'numeric'],
            'first_payment' => ['required', 'numeric'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $version = $proposal->versions()->create($validated);

        return new VersionResource($version);
    }
}
