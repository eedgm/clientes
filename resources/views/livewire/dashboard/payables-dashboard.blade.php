<div class="block w-full mt-4 overflow-auto scrolling-touch">
    <table class="table w-full table-list table-striped-primary">
        <thead class="text-gray-700 bg-gray-100 border-b-2 border-gray-300">
            <tr class="">
                <th class="px-4 py-3 text-left">
                    @lang('crud.product_payables.inputs.name')
                </th>
                <th class="px-4 py-3 text-left lg:w-24 ">
                    @lang('crud.payables.inputs.date')
                </th>
                <th class="px-4 py-3 text-left">
                    @lang('crud.payables.inputs.product_id')
                </th>
                <th class="px-4 py-3 text-right">
                    @lang('crud.payables.inputs.total')
                </th>
            </tr>
        </thead>
        <tbody class="text-gray-600">
            @foreach ($payables as $payable)
            <tr class="hover:bg-gray-100">
                <td class="px-4 py-3 text-left">
                    {{ $payable->name ?? '-' }}
                </td>
                <td class="px-4 py-3 text-left">
                    {{ $payable->date ? $payable->date->format('M d, y') : '-' }}
                </td>
                <td class="px-4 py-3 text-left">
                    {{ optional($payable->product)->name ?? '-' }}
                </td>
                <td class="px-4 py-3 text-right">
                    {{ $value = $payable->total }}
                </td>
            </tr>
            @php
                $total += $value;
            @endphp
            @endforeach
        </tbody>
    </table>
    <div class="pr-4 font-bold text-right text-red-500">$ {{ $total }}</div>

</div>
