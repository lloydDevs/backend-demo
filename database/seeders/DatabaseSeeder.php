<?php

namespace Database\Seeders;

use App\Modules\Booking\Database\Seeders\BookingDatabaseSeeder;
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
        $this->call(BookingDatabaseSeeder::class);
    }
}
