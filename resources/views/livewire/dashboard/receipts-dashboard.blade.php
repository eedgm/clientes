<div>
    <table class="table w-full table-list table-striped-primary">
        <thead class="text-gray-700 bg-gray-100 border-b-2 border-gray-300">
            <tr class="">
                <th class="px-4 py-3 text-left lg:w-24 ">
                    @lang('crud.receipts.date_dashboard')
                </th>
                <th class="px-4 py-3 text-right">
                    #
                </th>
                <th class="px-4 py-3 text-left">
                    @lang('crud.receipts.inputs.client_id')
                </th>
                <th class="px-4 py-3 text-right lg:w-32">
                    @lang('crud.receipts.total')
                </th>
            </tr>
        </thead>
        <tbody class="text-gray-600">
                @foreach ($receipts as $receipt)
                <tr class="hover:bg-gray-100 odd:bg-white even:bg-blue-50">
                    <td class="px-4 py-3 text-left">
                        {{ $receipt->real_date->format('M d') ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('edit', $receipt)
                            <x-links href="{{ route('receipts.edit', $receipt->id) }}">{{ $receipt->number ?? '-' }}</x-links>
                        @else
                            {{ $receipt->number ?? '-' }}
                        @endcan
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($receipt->client)->name ?? '-'
                        }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        $ {{
                            $value =
                                ($receipt->totalTickets()->first() ? $receipt->totalTickets()->first()->total : 0)
                                +
                                ($receipt->totalPayables()->first() ? $receipt->totalPayables()->first()->total : 0)
                        }}
                        @php
                            $total += $value;
                        @endphp
                    </td>
                </tr>
                @endforeach
        </tbody>
    </table>
    <div class="pr-4 font-bold text-right text-red-500">$ {{ $total }}</div>

</div>
