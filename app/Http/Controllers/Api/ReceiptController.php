<?php

namespace App\Http\Controllers\Api;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use App\Http\Resources\ReceiptCollection;
use App\Http\Requests\ReceiptStoreRequest;
use App\Http\Requests\ReceiptUpdateRequest;

class ReceiptController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Receipt::class);

        $search = $request->get('search', '');

        $receipts = Receipt::search($search)
            ->latest()
            ->paginate();

        return new ReceiptCollection($receipts);
    }

    /**
     * @param \App\Http\Requests\ReceiptStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceiptStoreRequest $request)
    {
        $this->authorize('create', Receipt::class);

        $validated = $request->validated();

        $receipt = Receipt::create($validated);

        return new ReceiptResource($receipt);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Receipt $receipt)
    {
        $this->authorize('view', $receipt);

        return new ReceiptResource($receipt);
    }

    /**
     * @param \App\Http\Requests\ReceiptUpdateRequest $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function update(ReceiptUpdateRequest $request, Receipt $receipt)
    {
        $this->authorize('update', $receipt);

        $validated = $request->validated();

        $receipt->update($validated);

        return new ReceiptResource($receipt);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Receipt $receipt)
    {
        $this->authorize('delete', $receipt);

        $receipt->delete();

        return response()->noContent();
    }
}
