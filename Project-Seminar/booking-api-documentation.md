# Booking System API Reference

**Base URL:** `http://localhost:8000/api/v1`

**Authentication:** None — public seminar API

**Content type:** `application/json`

**Rate limit:** 30 requests per minute per client

## `GET /customers`

Returns a list of all customers. Send the selected `id` as `customer_id` when creating or updating a Booking.

```json
{
  "data": [
    { "id": 1, "name": "Maria Santos", "email": "maria@example.com" }
  ]
}
```

## `POST /customers`

Creates a new Customer and returns `201 Created`.

```json
{
  "name": "Alanna Ebert",
  "email": "lmurray@example.net"
}
```

| Field | Required | Rules |
|---|---:|---|
| `name` | Yes | String, maximum 255 characters |
| `email` | Yes | Valid email, maximum 255 characters, unique |

## `GET /customers/{customer}`

Returns a single Customer by ID.

```json
{
  "data": {
    "id": 1,
    "name": "Maria Santos",
    "email": "maria@example.com"
  }
}
```

## `PUT/PATCH /customers/{customer}`

Updates an existing Customer's details. All fields are optional (`sometimes`).

```json
{
  "name": "Alanna Ebert",
  "email": "lmurray@example.net"
}
```

## `DELETE /customers/{customer}`

Deletes a Customer.

```json
{
  "message": "Customer deleted successfully."
}
```


## `GET /services`

Returns valid service values.

```json
{
  "data": [
    { "value": "conference_room_a", "label": "Conference Room A" },
    { "value": "massage_60min", "label": "Massage - 60 Minutes" }
  ]
}
```

## `GET /bookings`

Returns a paginated list, newest first. Optional parameters:

| Parameter | Description |
|---|---|
| `page` | Pagination page; defaults to `1` |
| `status` | `pending`, `confirmed`, `cancelled`, or `completed` |

Example: `GET /api/v1/bookings?status=confirmed`

## `POST /bookings`

Creates a pending Booking and returns `201 Created`.

```json
{
  "customer_id": 1,
  "service_name": "massage_60min",
  "booking_date": "2026-09-28",
  "booking_time": "10:30",
  "notes": "First appointment"
}
```

| Field | Required | Rules |
|---|---:|---|
| `customer_id` | Yes | Existing Customer ID |
| `service_name` | Yes | Valid value from `/services` |
| `booking_date` | Yes | Today or later; `YYYY-MM-DD` |
| `booking_time` | Yes | `HH:MM` |
| `notes` | No | String, maximum 1000 characters |

## `GET /bookings/{booking}`

Returns one Booking and its Customer.

## `PUT/PATCH /bookings/{booking}`

Updates any create field. All fields are optional. Use the status endpoint to change status.

## `PATCH /bookings/{booking}/status`

```json
{ "status": "confirmed" }
```

| Current | Allowed next status |
|---|---|
| `pending` | `confirmed`, `cancelled` |
| `confirmed` | `completed`, `cancelled` |
| `cancelled` | None |
| `completed` | None |

An invalid transition returns `422`.

## `DELETE /bookings/{booking}`

```json
{ "message": "Booking deleted successfully." }
```

## Booking response

```json
{
  "data": {
    "id": 1,
    "customer": {
      "id": 1,
      "name": "Maria Santos",
      "email": "maria@example.com"
    },
    "service_name": "massage_60min",
    "service_label": "Massage - 60 Minutes",
    "booking_date": "2026-09-28",
    "booking_time": "10:30",
    "status": "pending",
    "status_label": "Pending",
    "status_color": "bg-yellow-100 text-yellow-800",
    "notes": "First appointment",
    "created_at": "2026-09-20T10:00:00.000000Z",
    "updated_at": "2026-09-20T10:00:00.000000Z"
  }
}
```

## Errors

| Status | Meaning |
|---:|---|
| `404` | Booking was not found |
| `422` | Validation or transition failure |
| `429` | Rate limit exceeded |
| `500` | Unexpected server error |

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "customer_id": ["The selected customer id is invalid."]
  }
}
```
