---
domain: workplace
module: maintenance
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Facility Maintenance — Decisions

> Reconstructed from the flat source spec. Ratify during the v2 rebuild.

## ADR: Status is a real state machine

- **Context:** A maintenance request has a defined lifecycle with side effects at each step.
- **Decision:** Use `spatie/laravel-model-states` — `Reported → Assigned → InProgress → Resolved → Closed`, plus a reopen back to `Reported`.
- **Consequences:** Transition guards + side effects (notifications, after-photo prompt) live on state classes; illegal transitions are impossible.

## ADR: External contractor is free-text *(assumed)*

- **Decision:** Assignment to an external contractor is a plain `contractor` string, not a managed vendor record.
- **Consequences:** Cheap for v1; a real vendor/supplier link (procurement) is a future integration — see [[unknowns]].

## ADR: Preventive schedules materialise requests

- **Decision:** `RunMaintenanceSchedulesCommand` (daily 06:00) turns due schedules into requests and advances `next_due_at` transactionally.
- **Consequences:** Exactly one request per due date (idempotent); preventive + reactive work share one queue.

## ADR: Photo upload contract

- **Decision:** Before/after photos restricted to image MIME + size cap, stored under `companies/{id}/maintenance/`.
- **Consequences:** Tenant-isolated storage; no arbitrary file types.
