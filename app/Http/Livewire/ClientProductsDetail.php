<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientProductsDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Client $client;
    public Product $product;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Product';

    protected $rules = [
        'product.name' => ['required', 'max:255', 'string'],
        'product.url' => ['nullable', 'url'],
        'product.description' => ['nullable', 'max:255', 'string'],
        'product.direction' => ['nullable', 'max:255', 'string'],
        'product.RUC' => ['nullable', 'max:255', 'string'],
        'product.DV' => ['nullable', 'max:255', 'string'],
    ];

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->resetProductData();
    }

    public function resetProductData()
    {
        $this->product = new Product();

        $this->dispatchBrowserEvent('refresh');
    }

    public function newProduct()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.client_products.new_title');
        $this->resetProductData();

        $this->showModal();
    }

    public function editProduct(Product $product)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.client_products.edit_title');
        $this->product = $product;

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

        if (!$this->product->client_id) {
            $this->authorize('create', Product::class);

            $this->product->client_id = $this->client->id;
        } else {
            $this->authorize('update', $this->product);
        }

        $this->product->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Product::class);

        Product::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetProductData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->client->products as $product) {
            array_push($this->selected, $product->id);
        }
    }

    public function render()
    {
        return view('livewire.client-products-detail', [
            'products' => $this->client->products()->paginate(20),
        ]);
    }
}
