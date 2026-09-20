# Booking System API — Repository-Specific Implementation Plan

## Purpose

Implement the seminar’s standalone Booking REST API inside this repository without changing the existing Inertia/Product demo. The seminar notes mention `nwidart/laravel-modules`, but this project uses a manual modular-monolith architecture under `app/Modules`, so Booking must follow the existing module-provider and namespace conventions.

## Target outcome

Deliver a working, unauthenticated API at `/api/v1` with:

- `Booking` CRUD operations
- service-type lookup data
- validated status transitions
- pagination and status filtering
- consistent JSON responses for validation, not-found, rate-limit, and server errors
- `throttle:30,1` protection
- seeded demo data
- feature tests and a Postman collection for the one-hour presentation

The existing web/Inertia Product module, authentication, RBAC, and frontend pages remain unchanged. Booking endpoints are intentionally public because this is a focused one-hour backend seminar.

## Repository decisions

| Seminar brief | Repository implementation |
|---|---|
| `Modules/Booking` | `app/Modules/Booking` |
| `nwidart/laravel-modules` | Manual `BookingModuleServiceProvider`, registered in `bootstrap/providers.php` |
| Repository layer omitted for the talk | Keep the project’s existing Repository → Service → Controller convention; do not add extra abstraction beyond the boilerplate pattern |
| API controllers | `app/Modules/Booking/Http/Controllers/Api` |
| Module migrations | `app/Modules/Booking/Database/Migrations`, loaded by the module provider |
| Module routes | `app/Modules/Booking/routes/api.php`, loaded by the module provider |
| No authentication | Do not add login, tokens, users, policies, permissions, or `auth` middleware to Booking; retain JSON behavior for `/api/*` |
| Frontend integration out of scope | No Vue/Inertia pages or web routes for Booking |

## Planned module structure

```text
app/Modules/Booking/
├── Database/
│   ├── Factories/BookingFactory.php
│   ├── Migrations/xxxx_xx_xx_create_bookings_table.php
│   └── Seeders/BookingDatabaseSeeder.php
├── Enums/
│   ├── BookingStatus.php
│   └── ServiceType.php
├── Http/
│   ├── Controllers/Api/
│   │   ├── BookingController.php
│   │   └── ServiceController.php
│   ├── Requests/
│   │   ├── StoreBookingRequest.php
│   │   ├── UpdateBookingRequest.php
│   │   └── TransitionBookingStatusRequest.php
│   └── Resources/BookingResource.php
├── Models/Booking.php
├── Policies/                         # omitted unless the API scope later adds auth
├── Providers/BookingModuleServiceProvider.php
├── Repositories/
│   ├── BookingRepository.php
│   └── BookingRepositoryInterface.php
├── Services/BookingService.php
└── routes/api.php
```

## Implementation sequence

### Phase 1 — Confirm module wiring

1. Copy the proven provider, repository, service, and migration-loading patterns from `app/Modules/Product`.
2. Create `BookingModuleServiceProvider` to:
   - load module migrations;
   - load `routes/api.php` with the `api` middleware and `/api/v1` prefix;
   - bind `BookingRepositoryInterface` to `BookingRepository`.
3. Register the provider in `bootstrap/providers.php`.
4. Confirm the application still boots and the existing Product routes/tests remain unaffected.

### Phase 2 — Model and enums

1. Add the `bookings` migration with:
   - `id`, customer fields, `service_name`, `booking_date`, `booking_time`, `status`, nullable `notes`, timestamps;
   - a database default of `pending` for `status`;
   - indexes on `status` and `booking_date` where useful for the demo.
2. Add `BookingStatus` as a string-backed enum with:
   - `label()`;
   - `color()`;
   - a method or map describing allowed next states.
3. Add `ServiceType` as a string-backed enum with the six fixed services and `label()`.
4. Add `Booking` with fillable fields and enum casts for `status` and `service_name`.
5. Add a factory capable of generating valid dates, times, services, and all four statuses.

### Phase 3 — Validation, business logic, and responses

1. Implement `StoreBookingRequest`:
   - required customer and booking fields;
   - `Rule::enum(ServiceType::class)` for `service_name`;
   - `after_or_equal:today` for `booking_date`;
   - no client-controlled `status`.
2. Implement `UpdateBookingRequest` with partial fields and the same field constraints; explicitly exclude `status`.
3. Implement `TransitionBookingStatusRequest` using `Rule::enum(BookingStatus::class)`.
4. Implement `BookingService` for:
   - paginated listing and optional status filter;
   - create/update/delete;
   - transition validation before persistence;
   - a clear domain exception or validation failure for invalid transitions.
5. Implement `BookingResource` with the documented fields:
   - raw `service_name` and `status` values;
   - `service_label`, `status_label`, and `status_color`;
   - date/time and timestamp formatting consistent with the API contract.
6. Implement controllers that only coordinate request → service → resource and return `201` for create and `200` for other successful operations.

### Phase 4 — Routes and HTTP behavior

Add these routes in `app/Modules/Booking/routes/api.php`:

| Method | URI | Handler |
|---|---|---|
| GET | `/services` | `ServiceController@index` |
| GET | `/bookings` | `BookingController@index` |
| POST | `/bookings` | `BookingController@store` |
| GET | `/bookings/{booking}` | `BookingController@show` |
| PUT/PATCH | `/bookings/{booking}` | `BookingController@update` |
| DELETE | `/bookings/{booking}` | `BookingController@destroy` |
| PATCH | `/bookings/{booking}/status` | `BookingController@updateStatus` |

Wrap the routes in `throttle:30,1`; do not attach authentication or authorization middleware. Use route model binding for `{booking}` so missing records become JSON `404` responses. Verify the route prefix produces `/api/v1/...` exactly once.

### Phase 5 — JSON error contract

Extend the existing exception configuration only as needed for API requests. Preserve the current Inertia error-page behavior for web requests. For `/api/*`, verify these shapes and status codes:

- `422`: Laravel validation response with `message` and `errors`;
- `404`: `{ "message": "Booking not found." }` or the agreed generic not-found message;
- `429`: `{ "message": "Too many requests. Please try again later." }` plus rate-limit headers;
- `500`: JSON message rather than an HTML error page when the API expects JSON.

Avoid a global response change that would alter the existing web/Inertia behavior.

### Phase 6 — Seed data and presentation fixtures

1. Seed 15–20 bookings across every service and status.
2. Ensure there are enough rows to demonstrate pagination and at least one confirmed booking for filtering.
3. Make seeded dates valid relative to the current date, or use an explicit fixed date strategy in tests so the suite remains deterministic.
4. Decide whether the Booking seeder is called from the root `DatabaseSeeder` by default; recommended for the seminar branch so `migrate:fresh --seed` produces a ready demo database.

### Phase 7 — Tests and Postman

Add `tests/Feature/BookingApiTest.php` covering:

- service lookup shape;
- paginated index and status filter;
- show/create/update/delete happy paths;
- invalid email, enum, date, and notes validation;
- status transition matrix, including terminal states;
- route-model-binding `404` JSON;
- `201` create response;
- throttle behavior and headers, using a focused test configuration to avoid slowing the full suite.

Create a Postman collection with variables for `base_url` and `booking_id`, plus saved examples for:

1. services;
2. paginated bookings;
3. filtered bookings;
4. create success;
5. validation failure;
6. show/update;
7. valid status transition;
8. invalid status transition;
9. delete;
10. rate-limit failure.

## Acceptance checklist

- [ ] `php artisan route:list` shows all `/api/v1` endpoints.
- [ ] `php artisan migrate:fresh --seed` creates the Booking table and demo rows.
- [ ] Existing Product and authentication tests still pass.
- [ ] Booking feature tests pass against the project’s SQLite test database.
- [ ] Every documented success response matches the API documentation.
- [ ] Invalid transitions cannot be persisted.
- [ ] `status_color` and `service_label` come from enums, not duplicated controller logic.
- [ ] API errors are JSON while existing Inertia errors remain unchanged.
- [ ] Postman can execute the full presentation flow without authentication.

## Suggested presentation order

1. Show the manual module structure and provider registration.
2. Show enums and explain backend-owned valid options.
3. Call `GET /services` and `GET /bookings`.
4. Create a booking, then deliberately trigger a `422`.
5. Show the resource output, including labels and badge color.
6. Update details and transition status.
7. Attempt an invalid transition and explain service-layer business rules.
8. Demonstrate pagination/filtering and the `429` throttle response.
9. Delete the booking and close with the separation between module boundaries and API consumers.

## Risks and mitigations

- **Architecture mismatch:** do not install `nwidart/laravel-modules`; use the existing manual module provider.
- **API/web regression:** keep all Booking routes under `/api/*` and limit exception changes to JSON requests.
- **Time-dependent validation:** use `Carbon::setTestNow()` in tests or generate dates from `today()` in factories.
- **Rate-limit demo flakiness:** use a dedicated throttle test and reset the limiter between scenarios.
- **Over-scoping the seminar:** leave authentication, authorization, users, tokens, queues, events, caching, frontend work, and repository layering beyond the existing project pattern out of this implementation.

## Relationship demonstration

The Booking module includes a real Eloquent relationship for the seminar:

- `Customer` has many `Bookings`.
- `Booking` belongs to `Customer`.
- `bookings.customer_id` is a foreign key to `customers.id`.
- Booking requests send `customer_id`.
- Booking responses include the related `customer` object.

This gives the presentation a concrete relationship example without adding authentication or a frontend.
