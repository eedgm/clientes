<div>
    <div>
        @can('create', App\Models\Product::class)
        <button class="button" wire:click="newProduct">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan @can('delete-any', App\Models\Product::class)
        <button
            class="button button-danger"
                {{ empty($selected) ? 'disabled' : '' }}
            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
            wire:click="destroySelected"
        >
            <i class="mr-1 icon ion-md-trash text-primary"></i>
            @lang('crud.common.delete_selected')
        </button>
        @endcan
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <div>
                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="product.name"
                            label="Name"
                            wire:model="product.name"
                            maxlength="255"
                            placeholder="Name"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.url
                            name="product.url"
                            label="Url"
                            wire:model="product.url"
                            maxlength="255"
                            placeholder="Url"
                        ></x-inputs.url>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="product.RUC"
                            label="RUC"
                            maxlength="255"
                            placeholder="RUC"
                            wire:model="product.RUC"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="DV"
                            label="DV"
                            maxlength="255"
                            placeholder="DV"
                            wire:model="product.DV"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="direction"
                            label="Direction"
                            maxlength="255"
                            wire:model="product.direction"
                            ></x-inputs.textarea
                        >
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="product.description"
                            label="Description"
                            wire:model="product.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing) @can('view-any', App\Models\Ticket::class)
            <x-partials.card class="mt-5 shadow-none bg-gray-50">
                <h4 class="mb-3 text-sm font-bold text-gray-600">Tickets</h4>

                <livewire:product-tickets-detail :product="$product" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\Payable::class)
            <x-partials.card class="mt-5 shadow-none bg-gray-50">
                <h4 class="mb-3 text-sm font-bold text-gray-600">Payables</h4>

                <livewire:product-payables-detail :product="$product" />
            </x-partials.card>
            @endcan @endif
        </div>

        <div class="flex justify-between px-6 py-4 bg-gray-50">
            <button
                type="button"
                class="button"
                wire:click="$toggle('showingModal')"
            >
                <i class="mr-1 icon ion-md-close"></i>
                @lang('crud.common.cancel')
            </button>

            <button
                type="button"
                class="button button-primary"
                wire:click="save"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>

    <div class="block w-full mt-4 overflow-auto scrolling-touch">
        <table class="w-full max-w-full mb-4 bg-transparent">
            <thead class="text-gray-700">
                <tr>
                    <th class="w-1 px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            wire:model="allSelected"
                            wire:click="toggleFullSelection"
                            title="{{ trans('crud.common.select_all') }}"
                        />
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_products.inputs.name')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_products.inputs.url')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_products.inputs.description')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($products as $product)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            value="{{ $product->id }}"
                            wire:model="selected"
                        />
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $product->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $product->url ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $product->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $product)
                            <button
                                type="button"
                                class="button"
                                wire:click="editProduct({{ $product->id }})"
                            >
                                <i class="icon ion-md-create"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="px-4 mt-10">{{ $products->render() }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
