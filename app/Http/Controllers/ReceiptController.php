<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Receipt;
use Illuminate\Http\Request;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.receipts.index', compact('receipts', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Receipt::class);

        $clients = Client::pluck('name', 'id');

        return view('app.receipts.create', compact('clients'));
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

        return redirect()
            ->route('receipts.edit', $receipt)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Receipt $receipt)
    {
        $this->authorize('view', $receipt);

        return view('app.receipts.show', compact('receipt'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Receipt $receipt)
    {
        $this->authorize('update', $receipt);

        $clients = Client::pluck('name', 'id');

        return view('app.receipts.edit', compact('receipt', 'clients'));
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

        return redirect()
            ->route('receipts.edit', $receipt)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('receipts.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
