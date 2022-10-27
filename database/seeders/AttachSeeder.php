<?php

namespace Database\Seeders;

use App\Models\Attach;
use Illuminate\Database\Seeder;

class AttachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Attach::factory()
            ->count(5)
            ->create();
    }
}
