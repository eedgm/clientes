<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayableResource;
use App\Http\Resources\PayableCollection;

class SupplierPayablesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        $search = $request->get('search', '');

        $payables = $supplier
            ->payables()
            ->search($search)
            ->latest()
            ->paginate();

        return new PayableCollection($payables);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Supplier $supplier)
    {
        $this->authorize('create', Payable::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'date' => ['required', 'date'],
            'cost' => ['required', 'numeric'],
            'margin' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'supplier_id_reference' => ['nullable', 'max:255', 'string'],
            'periodicity' => ['required', 'in:month,year'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ]);

        $payable = $supplier->payables()->create($validated);

        return new PayableResource($payable);
    }
}
