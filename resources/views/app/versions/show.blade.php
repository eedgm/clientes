<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.versions.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('versions.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.proposal_id')
                        </h5>
                        <span
                            >{{ optional($version->proposal)->product_name ??
                            '-' }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.user_id')
                        </h5>
                        <span
                            >{{ optional($version->client)->name ?? '-' }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.attachment')
                        </h5>
                        @if($version->attachment)
                        <a
                            href="{{ \Storage::url($version->attachment) }}"
                            target="blank"
                            ><i class="mr-1 icon ion-md-download"></i
                            >&nbsp;Download</a
                        >
                        @else - @endif
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.total')
                        </h5>
                        <span>{{ $version->total ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.time')
                        </h5>
                        <span>{{ $version->time ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.cost_per_hour')
                        </h5>
                        <span>{{ $version->cost_per_hour ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.hour_per_day')
                        </h5>
                        <span>{{ $version->hour_per_day ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.months_to_pay')
                        </h5>
                        <span>{{ $version->months_to_pay ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.unexpected')
                        </h5>
                        <span>{{ $version->unexpected ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.company_gain')
                        </h5>
                        <span>{{ $version->company_gain ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.bank_tax')
                        </h5>
                        <span>{{ $version->bank_tax ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.versions.inputs.first_payment')
                        </h5>
                        <span>{{ $version->first_payment ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('versions.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Version::class)
                    <a href="{{ route('versions.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>

            @can('view-any', App\Models\Task::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Tasks </x-slot>

                <livewire:version-tasks-detail :version="$version" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\person_version::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> People </x-slot>

                <livewire:version-people-detail :version="$version" />
            </x-partials.card>
            @endcan
        </div>
    </div>
</x-app-layout>
