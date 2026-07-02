---
domain: events
module: venues
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Venues — API / DTOs

## `CreateVenueData`

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `address` | array | structured address |
| `capacity` | int | required; min:1 |
| `contact_name` | string | nullable |
| `contact_phone` | string | nullable; `phone:AUTO` → E.164 |
| `facilities` | array | nullable |
| `cost_cents` | int | nullable |
| `rooms` | array | of `{ name, capacity }` |

## Read API (internal)

- Venues + rooms are read by [[../events/_module|events.events]] (venue link + session room picker) through the Venues model/service.

## Public / Portal Endpoints

None. The venue address renders read-only on the public event landing (map/directions), served by the events landing page — no venues-owned public route.
