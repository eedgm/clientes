<?php

namespace App\Http\Controllers\Api;

use App\Models\Payable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayableResource;
use App\Http\Resources\PayableCollection;
use App\Http\Requests\PayableStoreRequest;
use App\Http\Requests\PayableUpdateRequest;

class PayableController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Payable::class);

        $search = $request->get('search', '');

        $payables = Payable::search($search)
            ->latest()
            ->paginate();

        return new PayableCollection($payables);
    }

    /**
     * @param \App\Http\Requests\PayableStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayableStoreRequest $request)
    {
        $this->authorize('create', Payable::class);

        $validated = $request->validated();

        $payable = Payable::create($validated);

        return new PayableResource($payable);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payable $payable
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Payable $payable)
    {
        $this->authorize('view', $payable);

        return new PayableResource($payable);
    }

    /**
     * @param \App\Http\Requests\PayableUpdateRequest $request
     * @param \App\Models\Payable $payable
     * @return \Illuminate\Http\Response
     */
    public function update(PayableUpdateRequest $request, Payable $payable)
    {
        $this->authorize('update', $payable);

        $validated = $request->validated();

        $payable->update($validated);

        return new PayableResource($payable);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payable $payable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Payable $payable)
    {
        $this->authorize('delete', $payable);

        $payable->delete();

        return response()->noContent();
    }
}
