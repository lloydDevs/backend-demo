<?php

namespace App\Modules\Booking\Database\Factories;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Enums\ServiceType;
use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'service_name' => fake()->randomElement(ServiceType::cases()),
            'booking_date' => fake()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
            'booking_time' => fake()->time('H:i'),
            'status' => fake()->randomElement(BookingStatus::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
