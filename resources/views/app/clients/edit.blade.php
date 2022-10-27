<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.clients.edit_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('clients.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <x-form
                    method="PUT"
                    action="{{ route('clients.update', $client) }}"
                    has-files
                    class="mt-4"
                >
                    @include('app.clients.form-inputs')

                    <div class="mt-10">
                        <a href="{{ route('clients.index') }}" class="button">
                            <i
                                class="
                                    mr-1
                                    icon
                                    ion-md-return-left
                                    text-primary
                                "
                            ></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('clients.create') }}" class="button">
                            <i class="mr-1 icon ion-md-add text-primary"></i>
                            @lang('crud.common.create')
                        </a>

                        <button
                            type="submit"
                            class="button button-primary float-right"
                        >
                            <i class="mr-1 icon ion-md-save"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
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
            @endcan @can('view-any', App\Models\User::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Users </x-slot>

                <livewire:client-users-detail :client="$client" />
            </x-partials.card>
            @endcan
        </div>
    </div>
</x-app-layout>
