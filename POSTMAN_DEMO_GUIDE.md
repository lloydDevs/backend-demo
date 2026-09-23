# Postman API Demo & Testing Cheat Sheet

This guide contains copy-pasteable payloads, field requirements, valid enum options, and step-by-step instructions for your live demonstration.

---

## ⚙️ Essential Postman Setup (Check Before Sending)

For every request in Postman:
1. **Headers Tab**:
   - `Accept`: `application/json`
   - `Content-Type`: `application/json`
2. **Body Tab**:
   - Select **`raw`** and choose **`JSON`** from the dropdown menu (Do NOT use `form-data` for PATCH/PUT).

---

## 1. Update Booking Details (PATCH)

Updates one or more fields of an existing booking.

- **Method:** `PATCH` (or `PUT`)
- **URL:** `http://127.0.0.1:8000/api/v1/bookings/{id}` *(Replace `{id}` with the booking ID, e.g. `4`)*

### Field Requirements for Updating Details

| Field | Required? | Allowed Values & Format | Notes |
| :--- | :---: | :--- | :--- |
| `service_name` | **Optional** | Valid enum string (see list below) | Do NOT use friendly labels like "Massage - 60 Min" |
| `booking_date` | **Optional** | `YYYY-MM-DD` (e.g. `2026-10-15`) | Must be today or a future date |
| `booking_time` | **Optional** | `HH:MM` in 24h format (e.g. `14:30`) | E.g. `09:00`, `14:30`, `18:00` |
| `notes` | **Optional** | String (up to 1000 characters) or `null` | Optional extra notes |
| `customer_id` | **Optional** | Existing customer ID integer (e.g. `1`, `2`, `3`) | Only if reassigning to another customer |

> ⚠️ **IMPORTANT:** Do NOT include `"status"` in this request. Status updates are ignored here and must be sent to the Status endpoint below.

### Example Payloads for Demo

#### Full Update Example:
```json
{
  "service_name": "massage_60min",
  "booking_date": "2026-10-15",
  "booking_time": "14:30",
  "notes": "Requested quiet room"
}
```

#### Partial Update (Single Field - Time Only):
```json
{
  "booking_time": "16:00"
}
```

#### Partial Update (Service & Notes Only):
```json
{
  "service_name": "conference_room_a",
  "notes": "Need projector and whiteboard"
}
```

---

## 2. Update Booking Status (PATCH)

Dedicated endpoint for updating booking lifecycle state with validation transition rules.

- **Method:** `PATCH`
- **URL:** `http://127.0.0.1:8000/api/v1/bookings/{id}/status` *(Replace `{id}` with booking ID, e.g. `4`)*

### Field Requirements

| Field | Required? | Allowed Values |
| :--- | :---: | :--- |
| `status` | **YES (Required)** | `"pending"`, `"confirmed"`, `"completed"`, `"cancelled"` |

### Allowed Status Transitions (State Machine)

| Current Status | Allowed Next Status |
| :--- | :--- |
| `pending` | `"confirmed"`, `"cancelled"` |
| `confirmed` | `"completed"`, `"cancelled"` |
| `completed` | *None (Terminal state)* |
| `cancelled` | *None (Terminal state)* |

### Example Payloads for Demo

#### Confirm a Pending Booking:
```json
{
  "status": "confirmed"
}
```

#### Cancel a Booking:
```json
{
  "status": "cancelled"
}
```

#### Complete a Confirmed Booking:
```json
{
  "status": "completed"
}
```

---

## 3. Create New Booking (POST)

Creates a new booking with initial status `pending`.

- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/v1/bookings`

### Field Requirements for Create

| Field | Required? | Type / Rules |
| :--- | :---: | :--- |
| `customer_id` | **YES** | Integer (must exist in `customers` table) |
| `service_name` | **YES** | Valid enum string |
| `booking_date` | **YES** | `YYYY-MM-DD` (today or future) |
| `booking_time` | **YES** | `HH:MM` |
| `notes` | **No** | String (optional) |

### Example Payload:
```json
{
  "customer_id": 1,
  "service_name": "massage_90min",
  "booking_date": "2026-10-20",
  "booking_time": "11:00",
  "notes": "First-time visitor"
}
```

---

## 📋 Valid Enum References

### Valid `service_name` Values:
- `"conference_room_a"` (Conference Room A)
- `"conference_room_b"` (Conference Room B)
- `"massage_60min"` (Massage - 60 Minutes)
- `"massage_90min"` (Massage - 90 Minutes)
- `"haircut_men"` (Men's Haircut)
- `"haircut_women"` (Women's Haircut)

### Valid `status` Values:
- `"pending"`
- `"confirmed"`
- `"completed"`
- `"cancelled"`

---

## 🚨 Common Demo Gotchas & Quick Troubleshooting

1. **Error: `MethodNotAllowedHttpException` (405)**
   - Cause: HTTP method is set to `GET` or `POST` instead of `PATCH`.
   - Fix: Change the dropdown to `PATCH`.

2. **Error: `422 Unprocessable Entity` - "Cannot transition from..."**
   - Cause: Attempting an illegal status jump (e.g. `completed` -> `pending` or `cancelled` -> `confirmed`).
   - Fix: Check current status and follow the transition table.

3. **Error: `422 Unprocessable Entity` - "The booking date must be a date after or equal to today."**
   - Cause: Date is in the past or formatted incorrectly.
   - Fix: Use format `YYYY-MM-DD` with a future date.

---

## 4. Create New Customer (POST)

- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/v1/customers`

### Example Payload:
```json
{
  "name": "Alanna Ebert",
  "email": "lmurray@example.net"
}
```

---

## 5. Update Customer Info (PATCH / PUT)

- **Method:** `PATCH` (or `PUT`)
- **URL:** `http://127.0.0.1:8000/api/v1/customers/1` *(Replace `1` with customer ID)*

### Example Payload:
```json
{
  "name": "Alanna Ebert",
  "email": "lmurray@example.net"
}
```

*(You can also send just `"name"` or just `"email"` for a partial update)*

---

## 6. Get Customer Details (GET)

- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/v1/customers/1`

