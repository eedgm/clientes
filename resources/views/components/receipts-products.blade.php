@props(['receipt'])

<td class="px-4 py-3 text-xs text-left">
    @php $products = []; @endphp
    @if($receipt->tickets)
        @foreach ($receipt->tickets as $ticket)
            @php $products[$ticket->product->name] = $ticket->product->name; @endphp
        @endforeach
    @endif
    @if($receipt->payables)
        @foreach ($receipt->payables as $payable)
            @php $products[$payable->product->name] = $payable->product->name; @endphp
        @endforeach
    @endif
    @foreach ($products as $product)
        {{ $product }},
    @endforeach
</td>
