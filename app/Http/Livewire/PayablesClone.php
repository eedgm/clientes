<?php

namespace App\Http\Livewire;

use App\Models\Payable;
use App\Models\Product;
use Livewire\Component;
use App\Models\Supplier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PayablesClone extends Component
{
    use AuthorizesRequests;

    public Payable $payable;
    public $productsForSelect = [];
    public $payableDate;

    public $selected = [];
    public $cloning = false;
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
        'payable.supplier_id' => ['required', 'exists:suppliers,id'],
        'payable.supplier_id_reference' => ['nullable', 'max:255', 'string'],
        'payable.periodicity' => ['required', 'in:month,year'],
    ];

    public function mount()
    {
        $this->productsForSelect = Product::pluck('name', 'id');
        $this->suppliersForSelect = Supplier::pluck('name', 'id');
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
        $this->cloning = false;
        $this->modalTitle = trans('crud.supplier_payables.new_title');
        $this->resetPayableData();

        $this->showModal();
    }

    public function clonePayable(Payable $payable)
    {
        $this->cloning = true;
        $this->modalTitle = trans('crud.supplier_payables.edit_title');
        $this->payable = $payable;

        if ($this->payable->periodicity == 'year') {
            $this->payableDate = $this->payable->date->addYear()->format('Y-m-d');
        } elseif ($this->payable->periodicity == 'month') {
            $this->payableDate = $this->payable->date->addMonth()->format('Y-m-d');
        }

        $this->dispatchBrowserEvent('refresh');

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

    public function updated($name, $value)
    {
        if ($name == 'payable.cost' ) {
            $this->payable->margin = 0;
        }
        if ($name == 'payable.margin') {
            if ($value >= 0) {
                $percentage = $this->payable->cost * ($value / 100);
                $this->payable->total = number_format($this->payable->cost + $percentage, 2);
            }
        }
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

        if (!$this->cloning) {
            $this->authorize('create', Payable::class);

            $this->payable->date = \Carbon\Carbon::parse($this->payableDate);

            $this->payable->save();

            $this->cloning = false;
        } elseif ($this->editing) {
            $this->authorize('update', $this->payable);

            $this->payable->date = \Carbon\Carbon::parse($this->payableDate);

            $this->payable->save();

            $this->editing = false;
        } else {
            $this->payable->date = \Carbon\Carbon::parse($this->payableDate);
            Payable::create([
                'name' => $this->payable->name,
                'date' => $this->payable->date,
                'cost' => $this->payable->cost,
                'margin' => $this->payable->margin,
                'total' => $this->payable->total,
                'product_id' => $this->payable->product_id,
                'supplier_id' => $this->payable->supplier_id,
                'supplier_id_reference' => $this->payable->supplier_id_reference,
                'periodicity' => $this->payable->periodicity
            ]);
        }

        $this->hideModal();
    }

    public function deletePayable(Payable $payable)
    {
        $this->authorize('delete', $payable);

        $payable->delete();
    }

    public function render()
    {
        $payables_without_receipt = Payable::whereNull('receipt_id')->with(['receipt'])->orderBy('date', 'desc')->get();
        $payables_with_receipt = Payable::whereNotNull('receipt_id')->with(['receipt'])->orderBy('date', 'desc')->get();
        return view('livewire.payables-clone', compact('payables_without_receipt', 'payables_with_receipt'));
    }
}
