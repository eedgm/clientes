<div class="block w-full mt-4 overflow-auto scrolling-touch">
    <table class="w-full max-w-full mb-4 bg-transparent">
        <thead class="text-gray-700 bg-gray-100 border-b-2 border-gray-300">
            <tr>
                <th></th>
                <th class="px-4 py-3 text-left">
                    @lang('crud.clients.name')
                </th>
                <th class="px-4 py-3 text-left">
                    @lang('crud.products.name')
                </th>
                <th class="px-4 py-3 text-left">
                    @lang('crud.product_tickets.inputs.description')
                </th>
                <th class="px-4 py-3 text-left w-60 ">
                    @lang('crud.product_tickets.inputs.statu_id')
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
                <tr class="hover:bg-gray-100 odd:bg-white even:bg-blue-50">
                    @php
                        switch (optional($ticket->priority)->name) {
                            case 'high':
                                $class = "text-red-500";
                                break;
                            case 'medium':
                                $class = "text-yellow-500";
                                break;
                            default:
                                $class = "text-green-500";
                                break;
                        }
                    @endphp
                    <td class="pl-3"><i class="bx bxs-circle {{ $class }}"></i></td>
                    <td class="px-4 py-3 text-left">
                        {{ $ticket->product->client->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $ticket->product->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ Str::limit($ticket->description, 100) ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        <x-inputs.select
                            name="statu_id"
                            wire:change="changeStatus($event.target.value, {{ $ticket->id }})"
                        >
                            @foreach($all_status as $value => $label)
                            <option {{ $ticket->statu_id == $value ? ' selected="selected"' : '' }} value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </td>
                    <td class="px-4 py-3 text-right" data-name="hours" data-id="{{ $ticket->id }}"contenteditable wire:blur="updateData($event.target.getAttribute('data-name'), $event.target.getAttribute('data-id'), $event.target.innerHTML)">
                        {{ $ticket->hours ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" data-name="total" data-id="{{ $ticket->id }}"contenteditable wire:blur="updateData($event.target.getAttribute('data-name'), $event.target.getAttribute('data-id'), $event.target.innerHTML)">
                        {{ $ticket->total ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        <x-inputs.date
                            name="finishedTicket"
                            wire:change="completed({{ $ticket->id }}, $event.target.value)"
                            value="{{ $ticket->finished_ticket ? date('Y-m-d', strtotime($ticket->finished_ticket)) : '' }}"
                        ></x-inputs.date>
                    </td>
                </tr>
                @endforeach
        </tbody>
    </table>
</div>
