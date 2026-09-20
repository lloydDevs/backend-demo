<?php

namespace App\Modules\Booking\Database\Seeders;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\Customer;
use Illuminate\Database\Seeder;

class BookingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory(10)->create()->each(function (Customer $customer) {
            Booking::factory(fake()->numberBetween(1, 3))->for($customer)->create();
        });
        Booking::factory()->create(['status' => BookingStatus::Confirmed]);
        Booking::factory()->create(['status' => BookingStatus::Completed]);
        Booking::factory()->create(['status' => BookingStatus::Cancelled]);
    }
}
