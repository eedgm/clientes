<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Icon;
use App\Models\User;
use App\Models\Color;
use App\Models\Statu;
use App\Models\Priority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Adding an admin user
        User::create(['name' => 'admin', 'email' => 'admin@admin.com', 'password' => Hash::make('admin')]);

        $this->call(PermissionsSeeder::class);

        // $this->call(ClientSeeder::class);
        // $this->call(PersonSeeder::class);
        Color::create(['name' => 'blue', 'code' => 'border-blue-200 text-blue-800 bg-blue-100']);
        Color::create(['name' => 'red', 'code' => 'border-red-200 text-red-800 bg-red-100']);
        Color::create(['name' => 'yellow', 'code' => 'border-yellow-200 text-yellow-800 bg-yellow-100']);
        Color::create(['name' => 'green', 'code' => 'border-green-200 text-green-800 bg-green-100']);
        Color::create(['name' => 'purple', 'code' => 'border-purple-200 text-purple-800 bg-purple-100']);
        Color::create(['name' => 'gray', 'code' => 'border-gray-200 text-gray-800 bg-gray-100']);
        Color::create(['name' => 'text blue', 'code' => 'text-blue-800']);
        Color::create(['name' => 'text red', 'code' => 'text-red-800']);
        Color::create(['name' => 'text yellow', 'code' => 'text-yellow-800']);
        Color::create(['name' => 'text green', 'code' => 'text-green-800']);
        Color::create(['name' => 'text purple', 'code' => 'text-purple-800']);
        Color::create(['name' => 'text gray', 'code' => 'text-gray-800']);
        Icon::create(['name' => 'alert', 'icon' => 'bx bx-error-alt']);
        Priority::create(['name' => 'high', 'color_id' => 1]);
        Priority::create(['name' => 'medium', 'color_id' => 2]);
        Priority::create(['name' => 'low', 'color_id' => 3]);
        Rol::create(['name' => 'CEO']);
        Rol::create(['name' => 'Administrator']);
        Rol::create(['name' => 'Developer']);
        Rol::create(['name' => 'Marketing']);
        Rol::create(['name' => 'Support']);
        Rol::create(['name' => 'TI']);
        Statu::create(['name' => 'Created', 'limit' => 2, 'color_id' => 7, 'icon_id' => 1]);
        Statu::create(['name' => 'In Progress', 'limit' => 0, 'color_id' => 8, 'icon_id' => 1]);
        Statu::create(['name' => 'Late', 'limit' => 1, 'color_id' => 9, 'icon_id' => 1]);
        Statu::create(['name' => 'Inactive', 'limit' => 3, 'color_id' => 10, 'icon_id' => 1]);
        Statu::create(['name' => 'Waiting for client', 'limit' => 2, 'color_id' => 11, 'icon_id' => 1]);
        Statu::create(['name' => 'Completed', 'limit' => 0, 'color_id' => 12, 'icon_id' => 1]);
    }
}
