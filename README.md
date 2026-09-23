# Booking System API

A Laravel 13, public JSON-only Booking API prepared for a one-hour backend seminar.

The project uses a manual modular-monolith structure. The Booking module is located in `app/Modules/Booking`; there is no frontend, authentication, authorization, or web UI in scope.

## Features

- Customer CRUD through `Route::apiResource()`
- Service-type lookup endpoint
- Booking CRUD through `Route::apiResource()`
- Customer → Booking Eloquent relationship
- Enum-backed service types and booking statuses
- Valid status-transition rules
- Pagination, filtering, JSON resources, validation, and rate limiting
- Seed data and Pest feature tests

## Documentation

Complete seminar documentation, API reference, and implementation notes are in [Project-Seminar](https://github.com/lloydDevs/backend-demo/tree/main/Project-Seminar).

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

The API is available at:

```text
http://localhost:8000/api/v1
```

Run the focused Booking API tests with:

```bash
php artisan test tests/Feature/BookingApiTest.php
```
