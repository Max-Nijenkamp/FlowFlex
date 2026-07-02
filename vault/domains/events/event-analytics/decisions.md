---
domain: events
module: event-analytics
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Event Analytics — Decisions

## ADR: No tables — pure read aggregation

- **Context:** Analytics derives everything from events/registrations/tickets/sponsors.
- **Decision:** Own no tables; compute metrics on read via `EventAnalyticsService`, reading sibling data through their owning services.
- **Consequences:** No write path, no ownership overlap; performance handled by caching, not materialized tables. See [[../../../security/data-ownership]].

## ADR: Cache with event-state-dependent TTL

- **Context:** Live events change fast; past events are static.
- **Decision:** Cache metrics 1 h for past events, 15 min for live; TTL-only invalidation.
- **Consequences:** Cheap reads; slight staleness on live dashboards (acceptable).

## ADR: Soft sections hide when a source module is off

- **Context:** Tickets/sponsors are optional.
- **Decision:** Revenue sections render only when the source module is active; the funnel/attendance core always renders.
- **Consequences:** Graceful degradation; dashboard never errors on a missing module.

## ADR: Attendance proxy for session popularity

- **Context:** Per-session check-in is deferred.
- **Decision (assumed):** Use overall attendance as a proxy for session popularity until session-level check-in exists.
- **Consequences:** Session popularity is approximate; revisit when [[../registrations/features/check-in|Check-In]] gains session scope. See [[unknowns]].
