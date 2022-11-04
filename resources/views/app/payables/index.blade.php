<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            @lang('crud.payables.index_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-9xl sm:px-6 lg:px-8">
            <x-partials.card>
                <div class="mt-4 mb-5">
                    <div class="flex flex-wrap justify-between">
                        <div class="md:w-1/2">
                            <form>
                                <div class="flex items-center w-full">
                                    <x-inputs.text
                                        name="search"
                                        value="{{ $search ?? '' }}"
                                        placeholder="{{ __('crud.common.search') }}"
                                        autocomplete="off"
                                    ></x-inputs.text>

                                    <div class="ml-1">
                                        <button
                                            type="submit"
                                            class="button button-primary"
                                        >
                                            <i class="icon ion-md-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="text-right md:w-1/2">
                            @can('create', App\Models\Payable::class)
                            <a
                                href="{{ route('payables.create') }}"
                                class="button button-primary"
                            >
                                <i class="mr-1 icon ion-md-add"></i>
                                @lang('crud.common.create')
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="block w-full overflow-auto scrolling-touch">
                    <table class="w-full max-w-full mb-4 bg-transparent">
                        <thead class="text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.name')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.date')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.payables.inputs.cost')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.payables.inputs.margin')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.payables.inputs.total')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.product_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.supplier_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.supplier_id_reference')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.periodicity')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.payables.inputs.receipt_id')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($payables as $payable)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-left">
                                    {{ $payable->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $payable->date ?? '-' }}
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
                                    {{ optional($payable->product)->name ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($payable->supplier)->name ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $payable->supplier_id_reference ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $payable->periodicity ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($payable->receipt)->id
                                    ?? '-' }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center"
                                    style="width: 134px;"
                                >
                                    <div
                                        role="group"
                                        aria-label="Row Actions"
                                        class="relative inline-flex align-middle "
                                    >
                                        @can('update', $payable)
                                        <a
                                            href="{{ route('payables.edit', $payable) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i
                                                    class="icon ion-md-create"
                                                ></i>
                                            </button>
                                        </a>
                                        @endcan @can('view', $payable)
                                        <a
                                            href="{{ route('payables.show', $payable) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i class="icon ion-md-eye"></i>
                                            </button>
                                        </a>
                                        @endcan @can('delete', $payable)
                                        <form
                                            action="{{ route('payables.destroy', $payable) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                        >
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="button"
                                            >
                                                <i
                                                    class="text-red-600  icon ion-md-trash"
                                                ></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11">
                                    <div class="px-4 mt-10">
                                        {!! $payables->render() !!}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
