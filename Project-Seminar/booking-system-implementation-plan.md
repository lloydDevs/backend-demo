# Booking System API — Implementation Plan
### Full Stack Development Seminar — Backend Presentation (Laravel)

**Presenter:** Dyoll (Backend) | **Duration:** 1 hour | **Format:** Standalone backend, Postman demo, no auth

---

## 1. Overview

This document outlines the implementation plan for a **Booking System REST API**, built with Laravel using a **modular architecture**. The backend is presented standalone — no frontend is wired to it during this session; a separate presenter demos frontend concepts using a different project afterward.

**Goals for the demo:**
- Demonstrate Laravel best practices without over-engineering
- Show a clean modular folder structure
- Highlight API design decisions that make life easier for frontend consumers (e.g., pre-computed status colors)
- Demonstrate proper HTTP status code handling
- Demonstrate pagination and rate limiting live via Postman

---

## 2. Scope

**In scope:**
- CRUD for a single `Booking` resource
- Status transition endpoint (`pending → confirmed → completed`, or `→ cancelled`)
- Pagination on the index endpoint
- Rate limiting (throttle middleware)
- Consistent JSON error responses with correct HTTP status codes
- Postman collection for live demo

**Out of scope (explicitly, to avoid over-engineering within 1 hour):**
- Authentication / authorization
- Repository pattern layered on top of services
- Queues, jobs, events/listeners
- Caching layer
- Frontend integration (handled by the other presenter, separate project)

---

## 3. Data Model — `Booking`

| Field | Type | Notes |
|---|---|---|
| `id` | bigint (PK) | Auto-increment |
| `customer_name` | string | Required |
| `customer_email` | string | Required, valid email format |
| `service_name` | string (backed enum value) | Required, one of the fixed `ServiceType` enum values |
| `booking_date` | date | Required, must be today or later |
| `booking_time` | time | Required |
| `status` | string (backed enum) | `pending`, `confirmed`, `cancelled`, `completed` — default `pending` |
| `notes` | text, nullable | Optional |
| `created_at` / `updated_at` | timestamp | Standard Laravel timestamps |

---

## 4. Module Structure

Using `nwidart/laravel-modules` convention:

```
Modules/Booking/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── BookingController.php
│   │   │       └── ServiceController.php
│   │   ├── Requests/
│   │   │   ├── StoreBookingRequest.php
│   │   │   └── UpdateBookingRequest.php
│   │   └── Resources/
│   │       └── BookingResource.php
│   ├── Models/
│   │   └── Booking.php
│   ├── Services/
│   │   └── BookingService.php
│   └── Enums/
│       ├── BookingStatus.php
│       └── ServiceType.php
├── database/
│   ├── migrations/
│   │   └── xxxx_xx_xx_create_bookings_table.php
│   └── seeders/
│       └── BookingDatabaseSeeder.php
├── routes/
│   └── api.php
└── config/
    └── config.php
```

**Layering rationale (for narration during the talk):**
- **Controller** — orchestrates only: validates via FormRequest, delegates to Service, returns Resource
- **Service** — owns business logic (e.g., status transition rules)
- **Model** — Eloquent model, casts, relationships (none needed here, kept simple)
- **Request** — validation + authorization rules, separated from controller
- **Resource** — controls exact API output shape, including computed fields
- **Enum** — single source of truth for status values, labels, and Tailwind badge colors

---

## 5. API Endpoints

Base path: `/api/v1/bookings`

| Method | Endpoint | Purpose | Success Code |
|---|---|---|---|
| GET | `/bookings` | Paginated list, optional `?status=` filter | 200 |
| GET | `/bookings/{booking}` | Show single booking (route model binding) | 200 |
| POST | `/bookings` | Create a booking | 201 |
| PUT/PATCH | `/bookings/{booking}` | Update booking details | 200 |
| DELETE | `/bookings/{booking}` | Delete a booking | 200 |
| PATCH | `/bookings/{booking}/status` | Dedicated status transition endpoint | 200 |
| GET | `/services` | Fixed list of valid service types, for frontend dropdown | 200 |

**Rate limiting:** `throttle:30,1` (30 requests per minute) applied to the entire route group.

---

## 6. Service Type Enum & Dropdown Endpoint

`service_name` is not free text — it's a fixed set of options controlled by the backend, defined in a `ServiceType` backed enum. This lets the frontend build its booking-service dropdown directly from the API instead of hardcoding options client-side.

| Value | Label |
|---|---|
| `conference_room_a` | Conference Room A |
| `conference_room_b` | Conference Room B |
| `massage_60min` | Massage - 60 Minutes |
| `massage_90min` | Massage - 90 Minutes |
| `haircut_men` | Men's Haircut |
| `haircut_women` | Women's Haircut |

**`GET /api/v1/services` response:**
```json
{
  "data": [
    { "value": "conference_room_a", "label": "Conference Room A" },
    { "value": "conference_room_b", "label": "Conference Room B" },
    { "value": "massage_60min", "label": "Massage - 60 Minutes" },
    { "value": "massage_90min", "label": "Massage - 90 Minutes" },
    { "value": "haircut_men", "label": "Men's Haircut" },
    { "value": "haircut_women", "label": "Women's Haircut" }
  ]
}
```

The `Booking` model casts `service_name` to `ServiceType`, and `StoreBookingRequest`/`UpdateBookingRequest` validate it with Laravel's `Enum` validation rule — the same pattern used for `status`. `BookingResource` returns both the raw value and its label:

```php
'service_name' => $this->service_name->value,
'service_label' => $this->service_name->label(),
```

**Demo narration point:** the frontend calls `GET /services` once to populate the dropdown, then submits the `value` back on create/update — backend stays the single source of truth for valid service options.

---

## 7. Status Enum & Tailwind Badge Mapping

The `BookingStatus` enum is the single source of truth for status value, display label, and Tailwind CSS badge class — computed once on the backend so the frontend needs zero status logic.

| Status | Label | Tailwind Classes |
|---|---|---|
| `pending` | Pending | `bg-yellow-100 text-yellow-800` |
| `confirmed` | Confirmed | `bg-green-100 text-green-800` |
| `cancelled` | Cancelled | `bg-red-100 text-red-800` |
| `completed` | Completed | `bg-blue-100 text-blue-800` |

**Allowed transitions (enforced in `BookingService`):**
- `pending` → `confirmed` or `cancelled`
- `confirmed` → `completed` or `cancelled`
- `cancelled` / `completed` → no further transitions (terminal states)

---

## 8. API Response Shape

**Success (single resource):**
```json
{
  "data": {
    "id": 1,
    "customer_name": "Juan Dela Cruz",
    "customer_email": "juan@example.com",
    "service_name": "conference_room_a",
    "service_label": "Conference Room A",
    "booking_date": "2026-09-25",
    "booking_time": "14:00",
    "status": "pending",
    "status_label": "Pending",
    "status_color": "bg-yellow-100 text-yellow-800",
    "notes": null,
    "created_at": "2026-09-19T10:00:00.000000Z",
    "updated_at": "2026-09-19T10:00:00.000000Z"
  }
}
```

**Success (paginated list):** standard Laravel `AnonymousResourceCollection` shape — `data`, `links`, `meta` (includes `current_page`, `last_page`, `total`, `per_page`).

**Validation error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "customer_email": ["The customer email field must be a valid email address."]
  }
}
```

**Not found (404):**
```json
{ "message": "Booking not found." }
```

**Rate limit exceeded (429):**
```json
{ "message": "Too many requests. Please try again later." }
```

**Server error (500):**
```json
{ "message": "Something went wrong. Please try again later." }
```

---

## 9. HTTP Status Code Reference

| Code | Scenario |
|---|---|
| 200 | Successful GET, PUT/PATCH, DELETE |
| 201 | Successful POST (resource created) |
| 404 | Booking not found (invalid ID / route model binding failure) |
| 422 | Validation failure (FormRequest) |
| 429 | Rate limit exceeded |
| 500 | Unhandled server error, returned as JSON not HTML |

---

## 10. Demo Flow (Postman, ~1 hour)

1. **Intro (5 min)** — explain modular structure, walk through folder tree
2. **GET /services** (5 min) — show the fixed dropdown source list, explain frontend uses this to populate the service selector
3. **GET /bookings** (10 min) — show pagination, explain `meta`/`links`, filter by `?status=confirmed`
4. **POST /bookings** (10 min) — create a booking using a `service_name` value from step 2, trigger validation error deliberately to show 422 shape
5. **GET /bookings/{id}** (5 min) — show single resource, point out `status_color` and `service_label` fields for frontend
6. **PUT /bookings/{id}** (5 min) — update a field
7. **PATCH /bookings/{id}/status** (10 min) — valid transition, then attempt an invalid transition to show business-rule enforcement
8. **Rate limiting live demo** (5 min) — use Postman Runner to fire 31 requests in under a minute, show the 429 response
9. **DELETE /bookings/{id}** (5 min) — delete and confirm removal via GET
10. **Wrap-up / handoff to frontend presenter**

---

## 11. Deliverables Checklist

- [ ] Migration — `create_bookings_table`
- [ ] `BookingStatus` enum with `color()` and `label()` methods
- [ ] `ServiceType` enum with `label()` method
- [ ] `Booking` model with `status` and `service_name` cast to their enums
- [ ] `BookingDatabaseSeeder` — seed 15–20 sample rows across all statuses and service types
- [ ] `StoreBookingRequest` / `UpdateBookingRequest` — validate `service_name` against `ServiceType` enum
- [ ] `BookingResource` — include `service_label`
- [ ] `ServiceController` (Api) — single `index()` returning the fixed list
- [ ] `BookingService` — create, update, delete, transition logic
- [ ] `BookingController` (Api)
- [ ] `routes/api.php` with `throttle:30,1` group, including `/services`
- [ ] Custom JSON error rendering (422 / 404 / 429 / 500)
- [ ] Postman collection with saved example responses (success, 422, 429) as live-demo backup
