<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Payable;
use App\Models\Receipt;
use App\Models\Supplier;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductPayablesDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Product $product;
    public Payable $payable;
    public $suppliersForSelect = [];
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
        'payable.supplier_id' => ['required', 'exists:suppliers,id'],
        'payable.supplier_id_reference' => ['nullable', 'max:255', 'string'],
        'payable.periodicity' => ['required', 'in:month,year'],
        'payable.receipt_id' => ['nullable', 'exists:receipts,id'],
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->suppliersForSelect = Supplier::pluck('name', 'id');
        $this->receiptsForSelect = Receipt::pluck('description', 'id');
        $this->resetPayableData();
    }

    public function resetPayableData()
    {
        $this->payable = new Payable();

        $this->payableDate = null;
        $this->payable->supplier_id = null;
        $this->payable->periodicity = 'month';
        $this->payable->receipt_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newPayable()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.product_payables.new_title');
        $this->resetPayableData();

        $this->showModal();
    }

    public function editPayable(Payable $payable)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.product_payables.edit_title');
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

        if (!$this->payable->product_id) {
            $this->authorize('create', Payable::class);

            $this->payable->product_id = $this->product->id;
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

        foreach ($this->product->payables as $payable) {
            array_push($this->selected, $payable->id);
        }
    }

    public function render()
    {
        return view('livewire.product-payables-detail', [
            'payables' => $this->product->payables()->paginate(20),
        ]);
    }
}
