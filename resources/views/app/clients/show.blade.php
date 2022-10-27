<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.clients.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('clients.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.clients.inputs.logo')
                        </h5>
                        <x-partials.thumbnail
                            src="{{ $client->logo ? \Storage::url($client->logo) : '' }}"
                            size="150"
                        />
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.clients.inputs.name')
                        </h5>
                        <span>{{ $client->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.clients.inputs.cost_per_hour')
                        </h5>
                        <span>{{ $client->cost_per_hour ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.clients.inputs.owner')
                        </h5>
                        <span>{{ $client->owner ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.clients.inputs.email_contact')
                        </h5>
                        <span>{{ $client->email_contact ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.clients.inputs.description')
                        </h5>
                        <span>{{ $client->description ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('clients.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Client::class)
                    <a href="{{ route('clients.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>

            @can('view-any', App\Models\Product::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Products </x-slot>

                <livewire:client-products-detail :client="$client" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\Person::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> People </x-slot>

                <livewire:client-people-detail :client="$client" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\Proposal::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Proposals </x-slot>

                <livewire:client-proposals-detail :client="$client" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\Receipt::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Receipts </x-slot>

                <livewire:client-receipts-detail :client="$client" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\client_user::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Users </x-slot>

                <livewire:client-users-detail :client="$client" />
            </x-partials.card>
            @endcan
        </div>
    </div>
</x-app-layout>
