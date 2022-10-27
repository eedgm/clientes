<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VersionResource;
use App\Http\Resources\VersionCollection;

class UserVersionsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $versions = $user
            ->versions()
            ->search($search)
            ->latest()
            ->paginate();

        return new VersionCollection($versions);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->authorize('create', Version::class);

        $validated = $request->validate([
            'attachment' => ['file', 'max:1024', 'nullable'],
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

        $version = $user->versions()->create($validated);

        return new VersionResource($version);
    }
}
