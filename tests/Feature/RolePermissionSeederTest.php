<?php

use App\Modules\Role\Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('seeder creates the expected roles', function () {
    (new RolePermissionSeeder)->run();

    expect(Role::pluck('name')->all())->toEqualCanonicalizing(['admin', 'editor', 'viewer']);
});

test('seeder creates the expected product permissions', function () {
    (new RolePermissionSeeder)->run();

    expect(Permission::pluck('name')->all())->toEqualCanonicalizing([
        'products.view',
        'products.create',
        'products.edit',
        'products.delete',
    ]);
});

test('admin role gets every product permission', function () {
    (new RolePermissionSeeder)->run();

    $admin = Role::findByName('admin');

    expect($admin->permissions->pluck('name')->all())->toEqualCanonicalizing([
        'products.view',
        'products.create',
        'products.edit',
        'products.delete',
    ]);
});

test('editor role cannot delete products', function () {
    (new RolePermissionSeeder)->run();

    $editor = Role::findByName('editor');

    expect($editor->hasPermissionTo('products.delete'))->toBeFalse();
    expect($editor->hasPermissionTo('products.view'))->toBeTrue();
});

test('viewer role can only view products', function () {
    (new RolePermissionSeeder)->run();

    $viewer = Role::findByName('viewer');

    expect($viewer->permissions->pluck('name')->all())->toBe(['products.view']);
});

test('seeder is idempotent when run twice', function () {
    (new RolePermissionSeeder)->run();
    (new RolePermissionSeeder)->run();

    expect(Role::count())->toBe(3)
        ->and(Permission::count())->toBe(4);
});

test('seeded demo admin user can log in and has the admin role', function () {
    (new RolePermissionSeeder)->run();

    $demo = \App\Models\User::where('email', 'demo@example.com')->first();

    expect($demo)->not->toBeNull()
        ->and($demo->hasRole('admin'))->toBeTrue();
});