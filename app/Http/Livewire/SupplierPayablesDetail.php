<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Payable;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupplierPayablesDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Supplier $supplier;
    public Payable $payable;
    public $productsForSelect = [];
    public $receiptsForSelect = [];
    public $payableDate;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Payable';

    protected $rules = [
        'payable.name' => ['required', 'max:255', 'string'],
        'payableDate' => ['required', 'date'],
        'payable.cost' => ['required', 'numeric'],
        'payable.margin' => ['required', 'numeric'],
        'payable.total' => ['required', 'numeric'],
        'payable.product_id' => ['required', 'exists:products,id'],
        'payable.supplier_id_reference' => ['nullable', 'max:255', 'string'],
        'payable.periodicity' => ['required', 'in:month,year'],
        'payable.receipt_id' => ['nullable', 'exists:receipts,id'],
    ];

    public function mount(Supplier $supplier)
    {
        $this->supplier = $supplier;
        $this->productsForSelect = Product::pluck('name', 'id');
        $this->receiptsForSelect = Receipt::pluck('description', 'id');
        $this->resetPayableData();
    }

    public function resetPayableData()
    {
        $this->payable = new Payable();

        $this->payableDate = null;
        $this->payable->product_id = null;
        $this->payable->periodicity = 'month';
        $this->payable->receipt_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newPayable()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.supplier_payables.new_title');
        $this->resetPayableData();

        $this->showModal();
    }

    public function editPayable(Payable $payable)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.supplier_payables.edit_title');
        $this->payable = $payable;

        $this->payableDate = $this->payable->date->format('Y-m-d');

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

        if (!$this->payable->supplier_id) {
            $this->authorize('create', Payable::class);

            $this->payable->supplier_id = $this->supplier->id;
        } else {
            $this->authorize('update', $this->payable);
        }

        $this->payable->date = \Carbon\Carbon::parse($this->payableDate);

        $this->payable->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Payable::class);

        Payable::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetPayableData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->supplier->payables as $payable) {
            array_push($this->selected, $payable->id);
        }
    }

    public function render()
    {
        return view('livewire.supplier-payables-detail', [
            'payables' => $this->supplier->payables()->paginate(20),
        ]);
    }
}
