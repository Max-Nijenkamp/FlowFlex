---
domain: events
module: event-analytics
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Event Analytics — API / DTOs

## `EventMetricsData` (output only)

Aggregated read model returned by `EventAnalyticsService::metrics`:

| Field | Type | Notes |
|---|---|---|
| `event_id` | ulid nullable | null = across-events |
| `views` | int | landing page views *(assumed source)* |
| `registrations` | int | |
| `confirmed` | int | |
| `attended` | int | |
| `attendance_rate` | float | attended / confirmed |
| `no_show_rate` | float | |
| `ticket_revenue` | Money | per event + per type (hidden if tickets inactive) |
| `sponsor_revenue` | Money | per tier (hidden if sponsors inactive) |

## Read API

- Input only: `metrics(?eventId, from, to)`. No input DTO beyond scalars.

## Public / Portal Endpoints

None. Analytics is internal; report export is a gated (throttled) dashboard action.
