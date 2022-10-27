<?php

namespace Database\Seeders;

use App\Models\Payable;
use Illuminate\Database\Seeder;

class PayableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payable::factory()
            ->count(5)
            ->create();
    }
}
