<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        // Spatie caches roles/permissions for 24h by default. RefreshDatabase
        // rolls back the DB after every test, so without this, test #2 can
        // see permission IDs from test #1 that no longer exist.
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    })
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Seed the same roles/permissions used in production
 * (app/Modules/Role/Database/Seeders/RolePermissionSeeder.php)
 * without also creating the demo user, so tests stay isolated.
 */
function seedRolesAndPermissions(): void
{
    $permissions = ['products.view', 'products.create', 'products.edit', 'products.delete'];

    foreach ($permissions as $permission) {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
        ->syncPermissions($permissions);

    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web'])
        ->syncPermissions(['products.view', 'products.create', 'products.edit']);

    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web'])
        ->syncPermissions(['products.view']);
}

/**
 * Create (and seed roles for) a user with the given role name.
 */
function userWithRole(string $role = 'admin'): \App\Models\User
{
    seedRolesAndPermissions();

    $user = \App\Models\User::factory()->create();
    $user->assignRole($role);

    return $user;
}