<?php

namespace App\Modules\Booking\Database\Seeders;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Models\Booking;
use Illuminate\Database\Seeder;

class BookingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Booking::factory(15)->create();
        Booking::factory()->create(['status' => BookingStatus::Confirmed]);
        Booking::factory()->create(['status' => BookingStatus::Completed]);
        Booking::factory()->create(['status' => BookingStatus::Cancelled]);
    }
}
