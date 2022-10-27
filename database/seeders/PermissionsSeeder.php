<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create default permissions
        Permission::create(['name' => 'list attaches']);
        Permission::create(['name' => 'view attaches']);
        Permission::create(['name' => 'create attaches']);
        Permission::create(['name' => 'update attaches']);
        Permission::create(['name' => 'delete attaches']);

        Permission::create(['name' => 'list attachments']);
        Permission::create(['name' => 'view attachments']);
        Permission::create(['name' => 'create attachments']);
        Permission::create(['name' => 'update attachments']);
        Permission::create(['name' => 'delete attachments']);

        Permission::create(['name' => 'list clients']);
        Permission::create(['name' => 'view clients']);
        Permission::create(['name' => 'create clients']);
        Permission::create(['name' => 'update clients']);
        Permission::create(['name' => 'delete clients']);

        Permission::create(['name' => 'list colors']);
        Permission::create(['name' => 'view colors']);
        Permission::create(['name' => 'create colors']);
        Permission::create(['name' => 'update colors']);
        Permission::create(['name' => 'delete colors']);

        Permission::create(['name' => 'list developers']);
        Permission::create(['name' => 'view developers']);
        Permission::create(['name' => 'create developers']);
        Permission::create(['name' => 'update developers']);
        Permission::create(['name' => 'delete developers']);

        Permission::create(['name' => 'list icons']);
        Permission::create(['name' => 'view icons']);
        Permission::create(['name' => 'create icons']);
        Permission::create(['name' => 'update icons']);
        Permission::create(['name' => 'delete icons']);

        Permission::create(['name' => 'list payables']);
        Permission::create(['name' => 'view payables']);
        Permission::create(['name' => 'create payables']);
        Permission::create(['name' => 'update payables']);
        Permission::create(['name' => 'delete payables']);

        Permission::create(['name' => 'list people']);
        Permission::create(['name' => 'view people']);
        Permission::create(['name' => 'create people']);
        Permission::create(['name' => 'update people']);
        Permission::create(['name' => 'delete people']);

        Permission::create(['name' => 'list priorities']);
        Permission::create(['name' => 'view priorities']);
        Permission::create(['name' => 'create priorities']);
        Permission::create(['name' => 'update priorities']);
        Permission::create(['name' => 'delete priorities']);

        Permission::create(['name' => 'list products']);
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'update products']);
        Permission::create(['name' => 'delete products']);

        Permission::create(['name' => 'list proposals']);
        Permission::create(['name' => 'view proposals']);
        Permission::create(['name' => 'create proposals']);
        Permission::create(['name' => 'update proposals']);
        Permission::create(['name' => 'delete proposals']);

        Permission::create(['name' => 'list receipts']);
        Permission::create(['name' => 'view receipts']);
        Permission::create(['name' => 'create receipts']);
        Permission::create(['name' => 'update receipts']);
        Permission::create(['name' => 'delete receipts']);

        Permission::create(['name' => 'list rols']);
        Permission::create(['name' => 'view rols']);
        Permission::create(['name' => 'create rols']);
        Permission::create(['name' => 'update rols']);
        Permission::create(['name' => 'delete rols']);

        Permission::create(['name' => 'list skills']);
        Permission::create(['name' => 'view skills']);
        Permission::create(['name' => 'create skills']);
        Permission::create(['name' => 'update skills']);
        Permission::create(['name' => 'delete skills']);

        Permission::create(['name' => 'list status']);
        Permission::create(['name' => 'view status']);
        Permission::create(['name' => 'create status']);
        Permission::create(['name' => 'update status']);
        Permission::create(['name' => 'delete status']);

        Permission::create(['name' => 'list suppliers']);
        Permission::create(['name' => 'view suppliers']);
        Permission::create(['name' => 'create suppliers']);
        Permission::create(['name' => 'update suppliers']);
        Permission::create(['name' => 'delete suppliers']);

        Permission::create(['name' => 'list tasks']);
        Permission::create(['name' => 'view tasks']);
        Permission::create(['name' => 'create tasks']);
        Permission::create(['name' => 'update tasks']);
        Permission::create(['name' => 'delete tasks']);

        Permission::create(['name' => 'list tickets']);
        Permission::create(['name' => 'view tickets']);
        Permission::create(['name' => 'create tickets']);
        Permission::create(['name' => 'update tickets']);
        Permission::create(['name' => 'delete tickets']);

        Permission::create(['name' => 'list versions']);
        Permission::create(['name' => 'view versions']);
        Permission::create(['name' => 'create versions']);
        Permission::create(['name' => 'update versions']);
        Permission::create(['name' => 'delete versions']);

        // Create user role and assign existing permissions
        $currentPermissions = Permission::all();
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo($currentPermissions);

        // Create admin exclusive permissions
        Permission::create(['name' => 'list roles']);
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'update roles']);
        Permission::create(['name' => 'delete roles']);

        Permission::create(['name' => 'list permissions']);
        Permission::create(['name' => 'view permissions']);
        Permission::create(['name' => 'create permissions']);
        Permission::create(['name' => 'update permissions']);
        Permission::create(['name' => 'delete permissions']);

        Permission::create(['name' => 'list users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        // Create admin role and assign all permissions
        $allPermissions = Permission::all();
        $adminRole = Role::create(['name' => 'super-admin']);
        $adminRole->givePermissionTo($allPermissions);

        $user = \App\Models\User::whereEmail('admin@admin.com')->first();

        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
