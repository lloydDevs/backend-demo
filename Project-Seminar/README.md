# Booking System API Seminar

A one-hour Laravel backend seminar project: a public, JSON-only API with no frontend, authentication, authorization, or web routes.

## Demonstrates

- Manual modular-monolith structure in `app/Modules/Booking`
- `Route::apiResource()` and route model binding
- Customer → Booking Eloquent relationship
- Form Requests, services, repositories, JSON resources, enums, pagination, filtering, throttling, and Pest tests

## API base URL

```text
http://localhost:8000/api/v1
```

## Documents

- [API reference](booking-api-documentation.md)
- [Implementation and presentation plan](booking-system-implementation-plan.md)
- [Repository architecture reference](booking-system-repo-implementation-plan.md)

## Commands

```bash
php artisan migrate:fresh --seed
php artisan serve
php artisan test tests/Feature/BookingApiTest.php
```
