<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Proposals
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 m-5 sm:grid-cols-1 lg:grid-cols-3">
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
                    <a href="{{ route('gantt', $proposal->id) }}"><i class="text-xl text-white bx bx-objects-horizontal-right"></i></a>
                </div>

            </x-partials.card>
        @endforeach
    </div>
</x-app-layout>
