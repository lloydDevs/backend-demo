<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Role\Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles, permissions, and a demo admin (demo@example.com / password).
        $this->call(RolePermissionSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Sample data so the Product module's CRUD has something to show.
        Product::factory(15)->create();
    }
}
