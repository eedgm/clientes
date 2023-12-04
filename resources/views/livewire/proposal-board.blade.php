<div>
    <div>
        @can('create', App\Models\Proposal::class)
        <button wire:click="newProposal" type="button" class="mt-3 ml-3 -mb-10 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
            @lang('crud.common.new')
            <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
        </button>
        @endcan
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <div class="flex flex-wrap">
                    <x-inputs.group class="w-1/2">
                        <x-inputs.text
                            name="proposal.product_name"
                            label="Product Name"
                            wire:model="proposal.product_name"
                            maxlength="255"
                            placeholder="Product Name"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-1/2">
                        <x-inputs.select
                            name="proposal.client_id"
                            label="Client"
                            wire:model="proposal.client_id"
                        >
                            <option value="null" disabled>Please select the Client</option>
                            @foreach($clients as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="proposal.description"
                            label="Description"
                            wire:model="proposal.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing) @can('view-any', App\Models\Version::class)
            <x-partials.card class="mt-5 shadow-none bg-gray-50">
                <h4 class="mb-3 text-sm font-bold text-gray-600">Versions</h4>

                <livewire:proposal-versions-detail :proposal="$proposal" />
            </x-partials.card>
            @endcan @endif
        </div>

        <div class="flex justify-between px-6 py-4 bg-gray-50">
            <button
                type="button"
                class="button"
                wire:click="$toggle('showingModal')"
            >
                <i class="mr-1 icon ion-md-close"></i>
                @lang('crud.common.cancel')
            </button>

            <button
                type="button"
                class="button button-primary"
                wire:click="save"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>

    <div class="block w-full mt-4 overflow-auto scrolling-touch">
        <div class="grid items-start grid-cols-1 gap-4 m-5 sm:grid-cols-1 lg:grid-cols-3">
            @foreach ($proposals as $proposal)
                <x-partials.card class="text-white bg-gradient-to-tr from-gray-700 via-gray-900 to-black">
                    <x-slot name="title">
                        {{ $proposal->client->name }} / {{ $proposal->product_name }}
                    </x-slot>

                    <div>
                        {{ $proposal->description }}
                    </div>
                    <div>
                        <span class="font-black">Versions:</span> {{ $proposal->versions->count() }}
                    </div>
                    <div>
                        <span class="font-black">Tasks:</span> {{ $proposal->tasks->count() }}
                    </div>
                    <div>
                        <span class="font-black">Hours:</span> {{ $proposal->tasks->sum('hours') }}
                    </div>

                    <div class="text-right">
                        <a href="{{ route('gantt', $proposal->id) }}"><i class="inline text-xl text-white bx bx-objects-horizontal-right"></i></a>
                        <a href="{{ route('proposal.kanban', $proposal->id) }}"><i class="inline text-xl text-white bx bx-columns"></i></a>
                        @can('delete', $proposal)
                        <form
                            action="{{ route('destroy-dashboard', $proposal) }}"
                            method="POST"
                            onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                            class="inline"
                        >
                            @csrf @method('DELETE')
                            <button
                                type="submit"
                                class=""
                            >
                                <i
                                    class="text-xl text-white bx bxs-trash"
                                ></i>
                            </button>
                        </form>
                        @endcan
                    </div>

                </x-partials.card>
            @endforeach
        </div>
    </div>
</div>
