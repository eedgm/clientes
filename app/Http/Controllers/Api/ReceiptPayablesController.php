<?php

namespace App\Http\Controllers\Api;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayableResource;
use App\Http\Resources\PayableCollection;

class ReceiptPayablesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Receipt $receipt)
    {
        $this->authorize('view', $receipt);

        $search = $request->get('search', '');

        $payables = $receipt
            ->payables()
            ->search($search)
            ->latest()
            ->paginate();

        return new PayableCollection($payables);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Receipt $receipt)
    {
        $this->authorize('create', Payable::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'date' => ['required', 'date'],
            'cost' => ['required', 'numeric'],
            'margin' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_id_reference' => ['nullable', 'max:255', 'string'],
            'periodicity' => ['required', 'in:month,year'],
        ]);

        $payable = $receipt->payables()->create($validated);

        return new PayableResource($payable);
    }
}
