<?php

namespace App\Modules\Role\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions(['products.view', 'products.create', 'products.edit']);

        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(['products.view']);

        // Demo user for quick login: demo@example.com / password
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo Admin', 'password' => bcrypt('password')]
        );

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
