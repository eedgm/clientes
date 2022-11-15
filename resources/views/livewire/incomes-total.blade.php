<div>
    <div class="grid grid-cols-1 gap-1 md:grid-cols-3">
        <x-partials.card class="bg-gradient-to-r from-pink-500 via-red-500 to-yellow-500">
            <h4 class="mb-3 text-lg font-bold text-white">
                Last Month Income
            </h4>

            <div class="text-4xl text-center text-white lg:text-7xl">
                $ {{ number_format($last_month_income, 2) }}
            </div>
        </x-partials.card>

        <x-partials.card class="bg-gradient-to-r from-slate-900 via-purple-900 to-slate-900">
            <h4 class="mb-3 text-lg font-bold text-white">
                This Month Income
            </h4>

            <div class="text-4xl text-center text-white lg:text-7xl">
                $ {{ number_format($this_month_income, 2) }}
            </div>
        </x-partials.card>

        <x-partials.card class="bg-gradient-to-r from-blue-700 via-blue-800 to-gray-900">
            <h4 class="mb-3 text-lg font-bold text-white">
                Tickets Without Receipt
            </h4>

            <div class="text-4xl text-center text-white lg:text-7xl">
                $ {{ number_format($tickets_total, 2) }}
            </div>
        </x-partials.card>
    </div>
</div>
