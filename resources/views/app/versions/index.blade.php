<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.versions.index_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <div class="mb-5 mt-4">
                    <div class="flex flex-wrap justify-between">
                        <div class="md:w-1/2">
                            <form>
                                <div class="flex items-center w-full">
                                    <x-inputs.text
                                        name="search"
                                        value="{{ $search ?? '' }}"
                                        placeholder="{{ __('crud.common.search') }}"
                                        autocomplete="off"
                                    ></x-inputs.text>

                                    <div class="ml-1">
                                        <button
                                            type="submit"
                                            class="button button-primary"
                                        >
                                            <i class="icon ion-md-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="md:w-1/2 text-right">
                            @can('create', App\Models\Version::class)
                            <a
                                href="{{ route('versions.create') }}"
                                class="button button-primary"
                            >
                                <i class="mr-1 icon ion-md-add"></i>
                                @lang('crud.common.create')
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="block w-full overflow-auto scrolling-touch">
                    <table class="w-full max-w-full mb-4 bg-transparent">
                        <thead class="text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.versions.inputs.proposal_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.versions.inputs.user_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.versions.inputs.attachment')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.total')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.versions.inputs.time')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.cost_per_hour')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.hour_per_day')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.months_to_pay')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.unexpected')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.company_gain')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.bank_tax')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.versions.inputs.first_payment')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($versions as $version)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-left">
                                    {{
                                    optional($version->proposal)->product_name
                                    ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($version->client)->name ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    @if($version->attachment)
                                    <a
                                        href="{{ \Storage::url($version->attachment) }}"
                                        target="blank"
                                        ><i
                                            class="mr-1 icon ion-md-download"
                                        ></i
                                        >&nbsp;Download</a
                                    >
                                    @else - @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->total ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $version->time ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->cost_per_hour ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->hour_per_day ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->months_to_pay ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->unexpected ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->company_gain ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->bank_tax ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->first_payment ?? '-' }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center"
                                    style="width: 134px;"
                                >
                                    <div
                                        role="group"
                                        aria-label="Row Actions"
                                        class="
                                            relative
                                            inline-flex
                                            align-middle
                                        "
                                    >
                                        @can('update', $version)
                                        <a
                                            href="{{ route('versions.edit', $version) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i
                                                    class="icon ion-md-create"
                                                ></i>
                                            </button>
                                        </a>
                                        @endcan @can('view', $version)
                                        <a
                                            href="{{ route('versions.show', $version) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i class="icon ion-md-eye"></i>
                                            </button>
                                        </a>
                                        @endcan @can('delete', $version)
                                        <form
                                            action="{{ route('versions.destroy', $version) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                        >
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="button"
                                            >
                                                <i
                                                    class="
                                                        icon
                                                        ion-md-trash
                                                        text-red-600
                                                    "
                                                ></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="13">
                                    <div class="mt-10 px-4">
                                        {!! $versions->render() !!}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
