<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayableResource;
use App\Http\Resources\PayableCollection;

class ProductPayablesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Product $product)
    {
        $this->authorize('view', $product);

        $search = $request->get('search', '');

        $payables = $product
            ->payables()
            ->search($search)
            ->latest()
            ->paginate();

        return new PayableCollection($payables);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
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
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ]);

        $payable = $product->payables()->create($validated);

        return new PayableResource($payable);
    }
}
