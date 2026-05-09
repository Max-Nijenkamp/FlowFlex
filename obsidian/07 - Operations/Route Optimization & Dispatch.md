---
tags: [flowflex, domain/operations, routing, dispatch, fleet, phase/5]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-08
---

# Route Optimization & Dispatch

Plan and dispatch the most efficient routes for field teams, delivery drivers, and service technicians. AI minimises drive time and fuel cost. Live GPS tracking, proof of delivery, and customer ETAs — all without a TomTom Telematics subscription.

**Who uses it:** Operations managers, dispatch coordinators, field service teams, delivery companies
**Filament Panel:** `operations`
**Depends on:** Core, [[Field Service Management]], [[Order Management]], [[Asset Management]]
**Phase:** 5

---

## Features

### Route Planning

- Input stops: upload CSV, import from Orders, or add manually
- Constraints: vehicle capacity (weight/volume), time windows per stop, driver working hours, break requirements
- Optimisation algorithm: nearest-neighbour + 2-opt improvement (runs in seconds for <200 stops)
- Multi-vehicle routing: assign stops across multiple drivers automatically
- Re-optimise: add an emergency stop → re-routes remaining stops
- Manual override: drag-and-drop stop reordering after AI suggestion

### Dispatching

- Dispatch view: all planned routes for today, status per driver
- Assign route to driver: push to driver's mobile app
- Driver mobile: see route, navigate with external maps app (Google Maps, Waze, HERE)
- Start route: driver confirms, timer starts
- Stop arrival / completion: driver marks each stop done with one tap
- Proof of delivery: photo capture, signature on screen, barcode scan

### Live Tracking

- Real-time GPS positions of all drivers on dispatch map (phone GPS)
- ETA per stop: recalculated live based on actual position and traffic
- Customer ETA notification: SMS/email with live tracking link sent when driver is N stops away
- Deviation alerts: driver off-route → alert to dispatch

### Time Windows

- Customer-requested time windows stored per stop (e.g. 9:00–12:00)
- Optimiser respects time windows, flags violations
- Automated customer notification if time window will be missed

### Proof of Delivery

- Photo: driver takes photo at delivery location
- Signature: customer signs on driver's phone screen
- Barcode: scan delivery package barcode
- Notes: driver adds delivery note
- All evidence stored in S3, linked to order record

### Analytics & Reporting

- Distance driven per driver / route / day
- On-time delivery rate
- Stops completed vs planned
- Fuel cost estimate (distance × cost per km)
- Driver performance: stops/hour, on-time rate, late deliveries

---

## Database Tables (3)

### `operations_routes`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `date` | date | |
| `driver_id` | ulid FK | → tenants |
| `vehicle_id` | ulid FK nullable | → it_assets |
| `status` | enum | `planned`, `in_progress`, `completed` |
| `optimised_at` | timestamp nullable | |
| `total_distance_km` | decimal nullable | |
| `started_at` | timestamp nullable | |
| `completed_at` | timestamp nullable | |

### `operations_route_stops`
| Column | Type | Notes |
|---|---|---|
| `route_id` | ulid FK | |
| `order_index` | integer | |
| `order_id` | ulid FK nullable | → ecommerce orders |
| `work_order_id` | ulid FK nullable | → field service |
| `address` | string | |
| `lat` | decimal nullable | |
| `lng` | decimal nullable | |
| `time_window_start` | time nullable | |
| `time_window_end` | time nullable | |
| `status` | enum | `pending`, `arrived`, `completed`, `failed` |
| `arrived_at` | timestamp nullable | |
| `completed_at` | timestamp nullable | |
| `pod_photo_file_id` | ulid FK nullable | |
| `pod_signature_file_id` | ulid FK nullable | |
| `notes` | text nullable | |

### `operations_driver_positions`
| Column | Type | Notes |
|---|---|---|
| `route_id` | ulid FK | |
| `driver_id` | ulid FK | |
| `lat` | decimal | |
| `lng` | decimal | |
| `speed_kmh` | decimal nullable | |
| `recorded_at` | timestamp | |

---

## Permissions

```
operations.routing.view
operations.routing.plan
operations.routing.dispatch
operations.routing.track-drivers
operations.routing.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | TomTom Telematics | Route4Me | OptimoRoute |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€/vehicle/mo) | ❌ (€39+/mo) | ❌ (€35+/mo) |
| AI route optimisation | ✅ | ✅ | ✅ | ✅ |
| Multi-vehicle | ✅ | ✅ | ✅ | ✅ |
| POD (photo + signature) | ✅ | ✅ | ✅ | ✅ |
| Integrated with orders | ✅ | ❌ | ❌ | ❌ |
| Customer ETA notification | ✅ | partial | ✅ | ✅ |

---

## Related

- [[Operations Overview]]
- [[Field Service Management]]
- [[Order Management]]
- [[Asset Management]]
- [[Supply Chain Visibility]]
