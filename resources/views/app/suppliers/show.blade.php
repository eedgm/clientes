<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.suppliers.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('suppliers.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.suppliers.inputs.name')
                        </h5>
                        <span>{{ $supplier->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.suppliers.inputs.url')
                        </h5>
                        <a
                            class="underline cursor-pointer"
                            target="_blank"
                            href="{{ $supplier->url }}"
                            >{{ $supplier->url ?? '-' }}</a
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.suppliers.inputs.login')
                        </h5>
                        <span>{{ $supplier->login ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.suppliers.inputs.description')
                        </h5>
                        <span>{{ $supplier->description ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('suppliers.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Supplier::class)
                    <a href="{{ route('suppliers.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>

            @can('view-any', App\Models\Payable::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Payables </x-slot>

                <livewire:supplier-payables-detail :supplier="$supplier" />
            </x-partials.card>
            @endcan
        </div>
    </div>
</x-app-layout>
