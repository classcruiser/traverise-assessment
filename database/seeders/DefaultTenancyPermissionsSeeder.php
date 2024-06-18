<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DefaultTenancyPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions_names = [
            'add booking', 'edit booking', 'view booking', 'delete booking',
            'add payment', 'edit payment', 'view payment', 'delete payment', 'confirm payment',
            'add guest', 'edit guest', 'view guest', 'delete guest',
            'add room', 'edit room', 'delete room',
        ];

        $permissions = collect($permissions_names)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'tenant', 'tenant_id' => null];
        });

        Permission::insert($permissions->toArray());
    }
}
