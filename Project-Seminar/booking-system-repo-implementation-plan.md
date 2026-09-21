# Booking System API — Repository Architecture Reference

## Architecture

This application uses a manual modular monolith. Booking is an application module at `app/Modules/Booking`, registered through `BookingModuleServiceProvider` in `bootstrap/providers.php`.

The provider loads `routes.php` and the module migrations and binds `BookingRepositoryInterface` to `BookingRepository`.

## Request flow

```text
HTTP request
  → routes.php (api + throttle middleware)
  → Form Request validation
  → API Controller
  → BookingService
  → BookingRepository / Eloquent
  → JsonResource response
```

## Layer responsibilities

| Layer | Responsibility |
|---|---|
| Routes | Version prefix, API middleware, throttle, resource routes |
| Controllers | Coordinate input and JSON responses |
| Form Requests | Validate IDs, enum values, dates, times, and notes |
| Service | Create, update, delete, filter, and transition rules |
| Repository | Booking persistence abstraction |
| Models | Enum casts and Customer/Booking relationship |
| Enums | Valid service values, labels, colors, and transitions |
| Resources | Stable API response shape |
| Seeder/tests | Demonstrable data and verified behavior |

## Relationship

```php
// Customer.php
public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}

// Booking.php
public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class, 'customer_id', 'id');
}
```

`bookings.customer_id` references `customers.id`. Booking responses embed the related Customer object.

## API boundary

All endpoints are intentionally public for this seminar. There are no authentication or frontend requirements. The `api` middleware enables route model binding for `{booking}`.

## Verification

```bash
php artisan migrate:fresh --seed
php artisan route:list --path=api/v1
php artisan test tests/Feature/BookingApiTest.php
```
