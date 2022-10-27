<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;
use Illuminate\Http\Request;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.payables.index', compact('payables', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Payable::class);

        $products = Product::pluck('name', 'id');
        $suppliers = Supplier::pluck('name', 'id');
        $receipts = Receipt::pluck('description', 'id');

        return view(
            'app.payables.create',
            compact('products', 'suppliers', 'receipts')
        );
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

        return redirect()
            ->route('payables.edit', $payable)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payable $payable
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Payable $payable)
    {
        $this->authorize('view', $payable);

        return view('app.payables.show', compact('payable'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payable $payable
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Payable $payable)
    {
        $this->authorize('update', $payable);

        $products = Product::pluck('name', 'id');
        $suppliers = Supplier::pluck('name', 'id');
        $receipts = Receipt::pluck('description', 'id');

        return view(
            'app.payables.edit',
            compact('payable', 'products', 'suppliers', 'receipts')
        );
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

        return redirect()
            ->route('payables.edit', $payable)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('payables.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
