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

it('supports sorting customers via query parameters', function () {
    Customer::query()->delete();
    $c1 = Customer::factory()->create(['name' => 'Zachary', 'email' => 'z@example.com']);
    $c2 = Customer::factory()->create(['name' => 'Alice', 'email' => 'a@example.com']);

    // Default sorting is by id asc
    $this->getJson('/api/v1/customers')
        ->assertOk()
        ->assertJsonPath('data.0.id', $c1->id)
        ->assertJsonPath('data.1.id', $c2->id);

    // Sort by name asc
    $this->getJson('/api/v1/customers?sort_by=name')
        ->assertOk()
        ->assertJsonPath('data.0.id', $c2->id)
        ->assertJsonPath('data.1.id', $c1->id);

    // Sort by id desc
    $this->getJson('/api/v1/customers?sort_by=id&sort_dir=desc')
        ->assertOk()
        ->assertJsonPath('data.0.id', $c2->id)
        ->assertJsonPath('data.1.id', $c1->id);
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
    $customer = Customer::factory()->create();
    $booking = $this->postJson('/api/v1/bookings', [
        'customer_id' => $customer->id,
        'service_name' => ServiceType::Massage60->value,
        'booking_date' => now()->addDay()->format('Y-m-d'),
        'booking_time' => '10:30',
    ])->assertCreated()->json('data');

    $this->getJson("/api/v1/bookings/{$booking['id']}")
        ->assertOk()
        ->assertJsonPath('data.customer.id', $customer->id);
});

it('enforces valid booking status transitions', function () {
    $customer = Customer::factory()->create();
    $booking = $this->postJson('/api/v1/bookings', [
        'customer_id' => $customer->id,
        'service_name' => ServiceType::Massage60->value,
        'booking_date' => now()->addDay()->format('Y-m-d'),
        'booking_time' => '10:30',
    ])->assertCreated()->json('data');

    $this->patchJson("/api/v1/bookings/{$booking['id']}/status", [
        'status' => BookingStatus::Confirmed->value,
    ])->assertOk()
        ->assertJsonPath('data.status', BookingStatus::Confirmed->value);

    $this->patchJson("/api/v1/bookings/{$booking['id']}/status", [
        'status' => BookingStatus::Pending->value,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

it('creates a new customer', function () {
    $response = $this->postJson('/api/v1/customers', [
        'name' => 'Alanna Ebert',
        'email' => 'lmurray@example.net',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Alanna Ebert')
        ->assertJsonPath('data.email', 'lmurray@example.net');
});

it('shows a customer by id', function () {
    $customer = Customer::factory()->create([
        'name' => 'Alanna Ebert',
        'email' => 'lmurray2@example.net',
    ]);

    $this->getJson("/api/v1/customers/{$customer->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $customer->id)
        ->assertJsonPath('data.name', 'Alanna Ebert')
        ->assertJsonPath('data.email', 'lmurray2@example.net');
});

it('updates customer info partially or fully', function () {
    $customer = Customer::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->patchJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Alanna Ebert',
        'email' => 'lmurray@example.net',
    ])->assertOk()
        ->assertJsonPath('data.id', $customer->id)
        ->assertJsonPath('data.name', 'Alanna Ebert')
        ->assertJsonPath('data.email', 'lmurray@example.net');
});

