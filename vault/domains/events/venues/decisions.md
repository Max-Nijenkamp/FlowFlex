---
domain: events
module: venues
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Venues — Decisions

## ADR: Reusable directory with delete-guard

- **Context:** Venues recur across events and must not vanish from a live event.
- **Decision:** `ev_venues` is a company-level directory; delete is blocked while upcoming events reference it *(assumed)*.
- **Consequences:** Stable `venue_id` references; soft-delete only when unreferenced.

## ADR: Rooms feed the session room picker

- **Context:** Multi-track events assign sessions to rooms.
- **Decision:** `ev_venue_rooms` (unique per venue by name) is the source for the session room picker in [[../events/_module|Events]].
- **Consequences:** Rooms are read cross-module; whether sessions store the room as an FK or a string is unresolved ([[../events/unknowns]]).

## ADR: CRUD-only, no service layer

- **Context:** Venues has no complex behaviour.
- **Decision:** Plain Filament resource CRUD; no interface→service. Events reads the model directly.
- **Consequences:** Minimal surface; matches the actions-vs-service guidance.
