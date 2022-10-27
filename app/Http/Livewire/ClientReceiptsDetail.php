<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Livewire\Component;
use App\Models\Receipt;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientReceiptsDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Client $client;
    public Receipt $receipt;
    public $receiptRealDate;
    public $receiptDateCharged;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Receipt';

    protected $rules = [
        'receipt.number' => ['required', 'numeric'],
        'receipt.description' => ['nullable', 'max:255', 'string'],
        'receiptRealDate' => ['required', 'date'],
        'receipt.charged' => ['required', 'boolean'],
        'receipt.reference_charged' => ['nullable', 'max:255', 'string'],
        'receiptDateCharged' => ['nullable', 'date'],
    ];

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->resetReceiptData();
    }

    public function resetReceiptData()
    {
        $this->receipt = new Receipt();

        $this->receiptRealDate = null;
        $this->receiptDateCharged = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newReceipt()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.client_receipts.new_title');
        $this->resetReceiptData();

        $this->showModal();
    }

    public function editReceipt(Receipt $receipt)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.client_receipts.edit_title');
        $this->receipt = $receipt;

        $this->receiptRealDate = $this->receipt->real_date->format('Y-m-d');
        $this->receiptDateCharged = $this->receipt->date_charged->format(
            'Y-m-d'
        );

        $this->dispatchBrowserEvent('refresh');

        $this->showModal();
    }

    public function showModal()
    {
        $this->resetErrorBag();
        $this->showingModal = true;
    }

    public function hideModal()
    {
        $this->showingModal = false;
    }

    public function save()
    {
        $this->validate();

        if (!$this->receipt->client_id) {
            $this->authorize('create', Receipt::class);

            $this->receipt->client_id = $this->client->id;
        } else {
            $this->authorize('update', $this->receipt);
        }

        $this->receipt->real_date = \Carbon\Carbon::parse(
            $this->receiptRealDate
        );
        $this->receipt->date_charged = \Carbon\Carbon::parse(
            $this->receiptDateCharged
        );

        $this->receipt->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Receipt::class);

        Receipt::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetReceiptData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->client->receipts as $receipt) {
            array_push($this->selected, $receipt->id);
        }
    }

    public function render()
    {
        return view('livewire.client-receipts-detail', [
            'receipts' => $this->client->receipts()->paginate(20),
        ]);
    }
}
