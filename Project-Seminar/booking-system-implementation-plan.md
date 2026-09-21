# Booking System API — Implementation and Presentation Plan

**Format:** One-hour Laravel backend seminar · public JSON API · Postman/browser demo · no frontend · no authentication

## Goal

Demonstrate a small but complete Booking API using this project's **manual modular-monolith architecture**. The module lives in `app/Modules/Booking` and is registered manually in `bootstrap/providers.php`.

## Scope

Included: customer lookup, service lookup, Booking CRUD, Customer/Booking relationship, status transitions, pagination, filtering, validation, JSON resources, throttling, seed data, and Pest feature tests.

Excluded: frontend pages, authentication, tokens, roles, permissions, policies, queues, events, caching, and external integrations.

## Domain model

```text
Customer 1 ────< many Bookings
```

`Booking` belongs to one Customer through `customer_id`. An API client obtains customer choices from `/customers`, then sends the selected ID when creating a Booking.

## Manual module structure

```text
app/Modules/Booking/
├── Database/{Factories,Migrations,Seeders}/
├── Enums/{BookingStatus,ServiceType}.php
├── Http/
│   ├── Controllers/Api/{Booking,Customer,Service}Controller.php
│   ├── Requests/{StoreBooking,UpdateBooking,TransitionBookingStatus}Request.php
│   └── Resources/{Booking,Customer}Resource.php
├── Models/{Booking,Customer}.php
├── Providers/BookingModuleServiceProvider.php
├── Repositories/{BookingRepository,BookingRepositoryInterface}.php
├── Services/BookingService.php
└── routes.php
```

The provider loads module routes and migrations and binds the Booking repository interface.

## Routes

```php
Route::prefix('api/v1')
    ->middleware(['api', 'throttle:30,1'])
    ->group(function () {
        Route::get('services', [ServiceController::class, 'index']);
        Route::get('customers', [CustomerController::class, 'index']);
        Route::apiResource('bookings', BookingController::class);
        Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    });
```

The `api` middleware is required for route model binding. `throttle:30,1` limits a client to 30 requests per minute.

## Endpoint summary

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/customers` | Customer choices for `customer_id` |
| GET | `/services` | Valid service enum values |
| GET | `/bookings` | Paginated list; optional `status` filter |
| POST | `/bookings` | Create a pending Booking |
| GET | `/bookings/{booking}` | Show a Booking with Customer |
| PUT/PATCH | `/bookings/{booking}` | Update details, not status |
| DELETE | `/bookings/{booking}` | Delete a Booking |
| PATCH | `/bookings/{booking}/status` | Apply a valid transition |

## Status rules

- pending → confirmed or cancelled
- confirmed → completed or cancelled
- cancelled and completed are terminal

## Suggested 60-minute flow

1. Show the manual Booking module and provider registration.
2. Show `Customer::bookings()` and `Booking::customer()`.
3. Call `GET /customers` and `GET /services`.
4. Create a Booking with `customer_id` and a service value.
5. Show the nested Customer in `GET /bookings/{booking}`.
6. Trigger a `422` validation error.
7. Demonstrate filtering, status transitions, and a rejected transition.
8. Demonstrate throttling, delete the Booking, and summarize the layers.

## Verification

```bash
php artisan migrate:fresh --seed
php artisan route:list --path=api/v1
php artisan test tests/Feature/BookingApiTest.php
```
