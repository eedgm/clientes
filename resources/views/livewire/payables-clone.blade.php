<div class="px-10">
    <div>
        @can('create', App\Models\Payable::class)
        <button class="button" wire:click="newPayable">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
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
                            name="payable.name"
                            label="Name"
                            wire:model="payable.name"
                            maxlength="255"
                            placeholder="Name"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.date
                            name="payableDate"
                            label="Date"
                            wire:model="payableDate"
                            max="255"
                        ></x-inputs.date>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="payable.cost"
                            label="Cost"
                            wire:model="payable.cost"
                            max="255"
                            step="0.01"
                            placeholder="Cost"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="payable.margin"
                            label="Margin"
                            wire:model="payable.margin"
                            max="255"
                            step="0.01"
                            placeholder="Margin"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="payable.total"
                            label="Total"
                            wire:model="payable.total"
                            max="255"
                            step="0.01"
                            placeholder="Total"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="payable.product_id"
                            label="Product"
                            wire:model="payable.product_id"
                        >
                            <option value="null" disabled>Please select the Product</option>
                            @foreach($productsForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="payable.supplier_id"
                            label="Supplier"
                            wire:model="payable.supplier_id"
                        >
                            <option value="null" disabled>Please select the Supplier</option>
                            @foreach($suppliersForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="payable.supplier_id_reference"
                            label="Supplier Id Reference"
                            wire:model="payable.supplier_id_reference"
                            maxlength="255"
                            placeholder="Supplier Id Reference"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="payable.periodicity"
                            label="Periodicity"
                            wire:model="payable.periodicity"
                        >
                            <option value="month" {{ $selected == 'month' ? 'selected' : '' }} >Month</option>
                            <option value="year" {{ $selected == 'year' ? 'selected' : '' }} >Year</option>
                        </x-inputs.select>
                    </x-inputs.group>
                </div>
            </div>
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

    <h2 class="p-3 text-white bg-green-700 rounded-xl">Payables without receipt</h2>
    <div class="block w-full mt-4 overflow-auto scrolling-touch">
        <table class="w-full max-w-full mb-4 bg-transparent">
            <thead class="text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.name')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.date')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.supplier_payables.inputs.cost')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.supplier_payables.inputs.margin')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.supplier_payables.inputs.total')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.product_id')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.supplier_id_reference')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.periodicity')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.receipt_id')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($payables_without_receipt as $payable)
                <tr class="hover:bg-gray-200">
                    <td class="px-4 py-3 text-left">
                        {{ $payable->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left truncate">
                        {{ $payable->date->format('Y-m-d') ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $payable->cost ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $payable->margin ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $payable->total ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($payable->product)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $payable->supplier_id_reference ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $payable->periodicity ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($payable->receipt)->id ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $payable)
                            <button
                                type="button"
                                class="button"
                                wire:click="editPayable({{ $payable->id }})"
                            >
                                <i class="text-xl text-green-600 bx bx-pencil hover:text-green-800"></i>
                            </button>
                            @endcan @can('delete', $payable)
                            <button
                                type="button"
                                class="button button-danger"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click="deletePayable({{ $payable->id }})"
                            >
                                <i class="text-xl text-white bx bx-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 class="p-3 text-white bg-red-700 rounded-xl">Payables with receipt</h2>
    <div class="block w-full mt-4 overflow-auto scrolling-touch">
        <table class="w-full max-w-full mb-4 bg-transparent">
            <thead class="text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.name')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.date')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.supplier_payables.inputs.cost')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.supplier_payables.inputs.margin')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.supplier_payables.inputs.total')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.product_id')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.supplier_id_reference')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.periodicity')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.supplier_payables.inputs.receipt_id')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($payables_with_receipt as $payable)
                <tr class="hover:bg-gray-200">
                    <td class="px-4 py-3 text-left">
                        {{ $payable->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left truncate">
                        {{ $payable->date->format('Y-m-d') ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $payable->cost ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $payable->margin ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $payable->total ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($payable->product)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $payable->supplier_id_reference ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $payable->periodicity ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($payable->receipt)->id ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $payable)
                            <button
                                type="button"
                                class="button"
                                wire:click="clonePayable({{ $payable->id }})"
                            >
                                <i class="text-xl text-green-600 bx bx-refresh hover:text-green-800"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
