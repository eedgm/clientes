<div>
    <div>
        @can('create', App\Models\Receipt::class)
        <button class="button" wire:click="newPayment">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.attach')
        </button>
        @endcan
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <h1 class="text-lg bold">Payables</h1>
                <table class="w-full max-w-full mb-4 bg-transparent">
                    <thead class="text-gray-700">
                        <tr>
                            <th class="w-1 px-4 py-3 text-left">
                                <input
                                    type="checkbox"
                                    wire:model="allSelectedPayables"
                                    wire:click="toggleFullSelectionPayables"
                                    title="{{ trans('crud.common.select_all') }}"
                                />
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_payables.inputs.name')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_payables.inputs.date')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_payables.inputs.cost')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_payables.inputs.margin')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_payables.inputs.total')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_payables.inputs.supplier_id')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_payables.inputs.supplier_id_reference')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_payables.inputs.periodicity')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        @foreach ($payables as $payable)
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-3 text-left">
                                <input
                                    type="checkbox"
                                    value="{{ $payable->id }}"
                                    wire:model="selectedPayable"
                                />
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $payable->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $payable->date ? date('Y-m-d', strtotime($payable->date)) : '-'}}
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
                                {{ optional($payable->supplier)->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $payable->supplier_id_reference ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $payable->periodicity ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <h1 class="text-lg bold">Tickets</h1>
                <table class="w-full max-w-full mb-4 bg-transparent">
                    <thead class="text-gray-700">
                        <tr>
                            <th class="w-1 px-4 py-3 text-left">
                                <input
                                    type="checkbox"
                                    wire:model="allSelectedTickets"
                                    wire:click="toggleFullSelectionTickets"
                                    title="{{ trans('crud.common.select_all') }}"
                                />
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.description')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.statu_id')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.priority_id')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_tickets.inputs.hours')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_tickets.inputs.total')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.finished_ticket')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        @foreach ($tickets as $ticket)
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-3 text-left">
                                <input
                                    type="checkbox"
                                    value="{{ $ticket->id }}"
                                    wire:model="selectedTicket"
                                />
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $ticket->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ optional($ticket->statu)->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ optional($ticket->priority)->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ $ticket->hours ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ $ticket->total ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $ticket->finished_ticket ? date('Y-m-d', strtotime($ticket->finished_ticket)) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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

    <div class="text-right">
        @if (isset($results['payables']) || isset($results['tickets']))
            <a class="px-5 py-2 text-white bg-blue-600 hover:shadow rounded-3xl" href="{{ route('client.receipt', $receipt_id) }}">Exportar a Pdf</a>
        @endif
    </div>

    <table class="w-full max-w-full mb-4 bg-transparent">
        <thead class="text-gray-700">
            <tr>
                <th class="px-4 py-3 text-left">
                    Producto
                </th>
                <th class="px-4 py-3 text-left">
                    Fecha
                </th>
                <th class="px-4 py-3 text-left">
                    Descripción
                </th>
                @if ($person)
                    <th class="px-4 py-3 text-left">
                        Solicitado por
                    </th>
                @endif
                @if ($hours)
                    <th class="px-4 py-3 text-right">
                        Horas
                    </th>
                @endif
                <th class="px-4 py-3 text-right">
                    Costo
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody class="text-gray-600">

            @if (isset($results['payables']))
                @foreach ($results['payables'] as $result)
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-3 text-left">{{ $result['product'] }}</td>
                        <td class="px-4 py-3 text-left">{{ $result['date'] }}</td>
                        <td class="px-4 py-3 text-left">{{ $result['description'] }}</td>
                        @if ($person)
                            <td class="px-4 py-3 text-left">{{ $result['person'] }}</td>
                        @endif
                        @if ($hours)
                            <td class="px-4 py-3 text-right">{{ $result['hours'] }}</td>
                        @endif
                        <td class="px-4 py-3 text-right">{{ $result['cost'] }}</td>
                        <td class="px-4 py-3 text-right">
                            <i
                                class="cursor-pointer bx bx-x hover:text-blue-600"
                                wire:click="removePayable({{ $result['id'] }})"
                                >
                            </i>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if (isset($results['tickets']))
                @foreach ($results['tickets'] as $result)
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-3 text-left">{{ $result['product'] }}</td>
                        <td class="px-4 py-3 text-left">{{ $result['date'] }}</td>
                        <td class="px-4 py-3 text-left">{{ $result['description'] }}</td>
                        @if ($person)
                            <td class="px-4 py-3 text-left">{{ $result['person'] }}</td>
                        @endif
                        @if ($hours)
                            <td class="px-4 py-3 text-right" data-name="hours" data-id="{{ $result['id'] }}"contenteditable wire:blur="updateData($event.target.getAttribute('data-name'), $event.target.getAttribute('data-id'), $event.target.innerHTML)">
                                {{ $result['hours'] }}
                            </td>
                        @endif
                        <td class="px-4 py-3 text-right" data-name="total" data-id="{{ $result['id'] }}"contenteditable wire:blur="updateData($event.target.getAttribute('data-name'), $event.target.getAttribute('data-id'), $event.target.innerHTML)">
                            {{ $result['cost'] }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <i
                                class="cursor-pointer bx bx-x hover:text-blue-600"
                                wire:click="removeTicket({{ $result['id'] }})"
                                >
                            </i>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="w-full pr-3 text-right">
        Total: $ {{ $total }}

    </div>
</div>
