<?php

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Enums\ServiceType;
use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\Customer;

it('returns customers for booking selection', function () {
    $customer = Customer::factory()->create([
        'name' => 'Maria Santos',
        'email' => 'maria@example.com',
    ]);

    $this->getJson('/api/v1/customers')
        ->assertOk()
        ->assertJsonPath('data.0.id', $customer->id)
        ->assertJsonPath('data.0.name', 'Maria Santos')
        ->assertJsonPath('data.0.email', 'maria@example.com');
});

it('creates a booking belonging to a customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->postJson('/api/v1/bookings', [
        'customer_id' => $customer->id,
        'service_name' => ServiceType::Massage60->value,
        'booking_date' => now()->addDay()->format('Y-m-d'),
        'booking_time' => '10:30',
        'notes' => 'First appointment',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.customer.id', $customer->id)
        ->assertJsonPath('data.customer.name', $customer->name)
        ->assertJsonPath('data.service_name', ServiceType::Massage60->value)
        ->assertJsonPath('data.status', BookingStatus::Pending->value);

    expect($customer->bookings()->count())->toBe(1);
});

it('rejects a booking with an unknown customer', function () {
    $this->postJson('/api/v1/bookings', [
        'customer_id' => 999999,
        'service_name' => ServiceType::Massage60->value,
        'booking_date' => now()->addDay()->format('Y-m-d'),
        'booking_time' => '10:30',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['customer_id']);
});

it('includes the customer relationship when showing a booking', function () {
    $booking = Booking::factory()->for(Customer::factory())->create([
        'status' => BookingStatus::Pending->value,
    ]);

    $this->getJson("/api/v1/bookings/{$booking->id}")
        ->assertOk()
        ->assertJsonPath('data.customer.id', $booking->customer_id);
});

it('enforces valid booking status transitions', function () {
    $booking = Booking::factory()->for(Customer::factory())->create([
        'status' => BookingStatus::Pending->value,
    ]);

    $this->patchJson("/api/v1/bookings/{$booking->id}/status", [
        'status' => BookingStatus::Confirmed->value,
    ])->assertOk()
        ->assertJsonPath('data.status', BookingStatus::Confirmed->value);

    $this->patchJson("/api/v1/bookings/{$booking->id}/status", [
        'status' => BookingStatus::Pending->value,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});
