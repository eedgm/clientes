<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use App\Http\Resources\ReceiptCollection;

class ClientReceiptsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Client $client)
    {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $receipts = $client
            ->receipts()
            ->search($search)
            ->latest()
            ->paginate();

        return new ReceiptCollection($receipts);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Client $client)
    {
        $this->authorize('create', Receipt::class);

        $validated = $request->validate([
            'number' => ['required', 'numeric'],
            'description' => ['nullable', 'max:255', 'string'],
            'real_date' => ['required', 'date'],
            'charged' => ['required', 'boolean'],
            'reference_charged' => ['nullable', 'max:255', 'string'],
            'date_charged' => ['nullable', 'date'],
        ]);

        $receipt = $client->receipts()->create($validated);

        return new ReceiptResource($receipt);
    }
}
