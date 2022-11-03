<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            @lang('crud.receipts.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-9xl sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('receipts.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <div class="px-4 mt-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.real_date')
                        </h5>
                        <span>{{ $receipt->real_date ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.number')
                        </h5>
                        <span>{{ $receipt->number ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.description')
                        </h5>
                        <span>{{ $receipt->description ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.client_id')
                        </h5>
                        <span
                            >{{ optional($receipt->client)->name ?? '-' }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.charged')
                        </h5>
                        <span>{{ $receipt->charged ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.reference_charged')
                        </h5>
                        <span>{{ $receipt->reference_charged ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.inputs.date_charged')
                        </h5>
                        <span>{{ $receipt->date_charged ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.receipts.total')
                        </h5>
                        <span>$ {{
                            ($receipt->totalTickets()->first() ? $receipt->totalTickets()->first()->total : 0)
                            +
                            ($receipt->totalPayables()->first() ? $receipt->totalPayables()->first()->total : 0)
                            }}</span>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('receipts.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Receipt::class)
                    <a href="{{ route('receipts.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>

        </div>
    </div>
</x-app-layout>
