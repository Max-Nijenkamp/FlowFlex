---
domain: crm
module: appointment-scheduling
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Appointment Scheduling — API

## DTOs

### BookSlotData (public input)

| Field | Type | Rules |
|---|---|---|
| meeting_type_slug | string | required, exists |
| scheduled_at | CarbonImmutable | required, future, on a free slot (validated in service) |
| name | string | required |
| email | string | required, email |
| notes | ?string | max:1000 |

Submitted from the public booking page. Rate-limited and honeypot-protected.

### BookingData (output)

| Field | Type | Notes |
|---|---|---|
| id | ulid | |
| meeting_type | string | |
| scheduled_at | CarbonImmutable | |
| assigned_rep | string | Round-robin result |
| contact_id | ulid | Find-or-created |
| status | string | |

## Public / Portal Endpoints

| Method | Route | Controller | Notes |
|---|---|---|---|
| GET | `/book/{company-slug}/{meeting-slug}` | `PublicBookingController@show` | Renders `Booking/Show.vue` with slot availability. |
| POST | `/book/{company-slug}/{meeting-slug}` | `PublicBookingController@store` | Accepts `BookSlotData`; rate-limited (`public-booking`). |
| GET | `/book/{company-slug}/{meeting-slug}/confirm/{booking}` | `PublicBookingController@confirm` | Renders `Booking/Confirm.vue`. |

Public routes sit on a guest/no-auth guard group, isolated from the app session guard. See [[security]].
