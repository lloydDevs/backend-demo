# Booking System API Documentation

**Version:** 1.0
**Base URL:** `http://localhost:8000/api/v1`
**Authentication:** None (public endpoints for demo purposes)
**Content-Type:** `application/json`
**Rate Limit:** 30 requests per minute per client (IP-based)

---

## Table of Contents

1. [Overview](#1-overview)
2. [Conventions](#2-conventions)
3. [Services](#3-services)
4. [Bookings](#4-bookings)
5. [Error Handling](#5-error-handling)
6. [Rate Limiting](#6-rate-limiting)
7. [Enum Reference](#7-enum-reference)
8. [Changelog](#8-changelog)

---

## 1. Overview

The Booking System API provides endpoints for managing service bookings — creating, viewing, updating, transitioning status, and deleting bookings. It also exposes a lookup endpoint for available service types, intended for populating frontend dropdowns.

All responses are JSON. All list endpoints are paginated. All write operations are validated server-side, with errors returned in a consistent shape described in [Section 5](#5-error-handling).

---

## 2. Conventions

- All requests and responses use `application/json`.
- Dates are formatted `YYYY-MM-DD`. Times are formatted `HH:MM` (24-hour).
- Timestamps (`created_at`, `updated_at`) are ISO 8601 UTC.
- All list responses are wrapped in a `data` array plus `links` and `meta` (standard Laravel pagination shape).
- All single-resource responses are wrapped in a `data` object.
- Status and service fields are always returned as both a machine-readable `value` and a human-readable `label` — use the label for display, the value for further API calls.

---

## 3. Services

Read-only endpoint for the fixed set of bookable service types. Intended for populating a dropdown/select input on the frontend.

### `GET /services`

Returns the full list of valid service types.

**Request**

No parameters.

```
GET /api/v1/services
```

**Response — `200 OK`**

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

**Usage note:** Call this once on page load to populate the service dropdown. Submit the `value` field (not the `label`) when creating or updating a booking.

---

## 3.1 Customers

### `GET /customers`

Returns the customer lookup list used by an API consumer when selecting a customer for a booking. Submit the selected `id` as `customer_id` when creating or updating a booking.

```http
GET /api/v1/customers
```

```json
{
  "data": [
    { "id": 1, "name": "Dr. Otis Rath II", "email": "wkulass@example.com" },
    { "id": 2, "name": "Maria Santos", "email": "maria@example.com" }
  ]
}
```

Authentication is intentionally omitted for this one-hour seminar.

## 4. Bookings

### 4.1 `GET /bookings`

Returns a paginated list of bookings, most recent first.

**Query Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `page` | integer | No | Page number. Defaults to `1`. |
| `status` | string | No | Filter by status. One of: `pending`, `confirmed`, `cancelled`, `completed`. |

**Request**

```
GET /api/v1/bookings?page=1&status=confirmed
```

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": 1,
      "customer": { "id": 1, "name": "Juan Dela Cruz", "email": "juan@example.com" },
      "service_name": "conference_room_a",
      "service_label": "Conference Room A",
      "booking_date": "2026-09-25",
      "booking_time": "14:00",
      "status": "confirmed",
      "status_label": "Confirmed",
      "status_color": "bg-green-100 text-green-800",
      "notes": null,
      "created_at": "2026-09-19T10:00:00.000000Z",
      "updated_at": "2026-09-19T10:05:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/v1/bookings?page=1",
    "last": "http://localhost:8000/api/v1/bookings?page=3",
    "prev": null,
    "next": "http://localhost:8000/api/v1/bookings?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25
  }
}
```

---

### 4.2 `GET /bookings/{id}`

Returns a single booking by ID.

**Path Parameters**

| Parameter | Type | Description |
|---|---|---|
| `id` | integer | The booking's ID |

**Request**

```
GET /api/v1/bookings/1
```

**Response — `200 OK`**

```json
{
  "data": {
    "id": 1,
    "customer": { "id": 1, "name": "Juan Dela Cruz", "email": "juan@example.com" },
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

**Error — `404 Not Found`** — see [Section 5](#5-error-handling)

---

### 4.3 `POST /bookings`

Creates a new booking.

**Request Body**

| Field | Type | Required | Rules |
|---|---|---|---|
| `customer_id` | integer | Yes | must reference an existing customer |
| `service_name` | string | Yes | must be a valid `ServiceType` value (see [Section 7](#7-enum-reference)) |
| `booking_date` | string | Yes | format `YYYY-MM-DD`, must be today or later |
| `booking_time` | string | Yes | format `HH:MM` |
| `notes` | string | No | max 1000 chars |

```json
{
  "customer_id": 2,
  "service_name": "massage_60min",
  "booking_date": "2026-09-28",
  "booking_time": "10:30",
  "notes": "First time customer"
}
```

**Response — `201 Created`**

```json
{
  "data": {
    "id": 26,
    "customer": { "id": 2, "name": "Maria Santos", "email": "maria@example.com" },
    "service_name": "massage_60min",
    "service_label": "Massage - 60 Minutes",
    "booking_date": "2026-09-28",
    "booking_time": "10:30",
    "status": "pending",
    "status_label": "Pending",
    "status_color": "bg-yellow-100 text-yellow-800",
    "notes": "First time customer",
    "created_at": "2026-09-19T11:00:00.000000Z",
    "updated_at": "2026-09-19T11:00:00.000000Z"
  }
}
```

**Note:** New bookings are always created with `status: pending`. Status cannot be set on creation — use the status transition endpoint ([4.6](#46-patch-bookingsidstatus)) afterward.

**Error — `422 Unprocessable Entity`** — see [Section 5](#5-error-handling)

---

### 4.4 `PUT /bookings/{id}`

Updates an existing booking's details. Does not change status — use the dedicated status endpoint for that.

**Path Parameters**

| Parameter | Type | Description |
|---|---|---|
| `id` | integer | The booking's ID |

**Request Body**

Same fields as [4.3 Create](#43-post-bookings), all optional on update (partial updates allowed).

```json
{
  "booking_time": "15:00",
  "notes": "Rescheduled at customer's request"
}
```

**Response — `200 OK`**

Returns the updated booking, same shape as [4.2](#42-get-bookingsid).

**Error — `404 Not Found`** or **`422 Unprocessable Entity`**

---

### 4.5 `DELETE /bookings/{id}`

Deletes a booking permanently.

**Path Parameters**

| Parameter | Type | Description |
|---|---|---|
| `id` | integer | The booking's ID |

**Request**

```
DELETE /api/v1/bookings/1
```

**Response — `200 OK`**

```json
{
  "message": "Booking deleted successfully."
}
```

**Error — `404 Not Found`**

---

### 4.6 `PATCH /bookings/{id}/status`

Transitions a booking to a new status. Enforces valid state transitions server-side.

**Path Parameters**

| Parameter | Type | Description |
|---|---|---|
| `id` | integer | The booking's ID |

**Request Body**

| Field | Type | Required | Rules |
|---|---|---|---|
| `status` | string | Yes | must be a valid `BookingStatus` value |

```json
{ "status": "confirmed" }
```

**Allowed transitions**

| From | To |
|---|---|
| `pending` | `confirmed`, `cancelled` |
| `confirmed` | `completed`, `cancelled` |
| `cancelled` | *(none — terminal state)* |
| `completed` | *(none — terminal state)* |

**Response — `200 OK`**

Returns the updated booking, same shape as [4.2](#42-get-bookingsid), reflecting the new status and its corresponding label/color.

**Error — `422 Unprocessable Entity`** — returned if the requested transition is not allowed:

```json
{
  "message": "This status transition is not allowed.",
  "errors": {
    "status": ["Cannot transition from 'completed' to 'pending'."]
  }
}
```

**Error — `404 Not Found`**

---

## 5. Error Handling

All errors follow a consistent JSON shape. The `errors` field is only present on validation failures (`422`).

| Status Code | Meaning | When it occurs |
|---|---|---|
| `404` | Not Found | Booking ID does not exist |
| `422` | Unprocessable Entity | Validation failure, or invalid status transition |
| `429` | Too Many Requests | Rate limit exceeded (30 req/min) |
| `500` | Internal Server Error | Unexpected server-side failure |

**Validation error example (`422`):**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "customer_id": ["The selected customer id is invalid."],
    "booking_date": ["The booking date field must be a date after or equal to today."]
  }
}
```

**Not found example (`404`):**

```json
{ "message": "Booking not found." }
```

**Rate limit example (`429`):**

```json
{ "message": "Too many requests. Please try again later." }
```

**Server error example (`500`):**

```json
{ "message": "Something went wrong. Please try again later." }
```

---

## 6. Rate Limiting

All endpoints are limited to **30 requests per minute** per client. When exceeded, the API returns `429 Too Many Requests`.

Rate limit status is included in the response headers of every request:

| Header | Description |
|---|---|
| `X-RateLimit-Limit` | Maximum requests allowed per window (30) |
| `X-RateLimit-Remaining` | Requests remaining in the current window |
| `Retry-After` | Seconds until the limit resets (only present on `429` responses) |

---

## 7. Enum Reference

### `BookingStatus`

| Value | Label | Tailwind Badge Classes |
|---|---|---|
| `pending` | Pending | `bg-yellow-100 text-yellow-800` |
| `confirmed` | Confirmed | `bg-green-100 text-green-800` |
| `cancelled` | Cancelled | `bg-red-100 text-red-800` |
| `completed` | Completed | `bg-blue-100 text-blue-800` |

### `ServiceType`

| Value | Label |
|---|---|
| `conference_room_a` | Conference Room A |
| `conference_room_b` | Conference Room B |
| `massage_60min` | Massage - 60 Minutes |
| `massage_90min` | Massage - 90 Minutes |
| `haircut_men` | Men's Haircut |
| `haircut_women` | Women's Haircut |

---

## 8. Changelog

| Version | Date | Notes |
|---|---|---|
| 1.0 | 2026-09-19 | Initial release — Bookings CRUD, status transitions, services lookup, pagination, rate limiting |
