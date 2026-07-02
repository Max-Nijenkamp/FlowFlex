---
domain: workplace
module: desk-booking
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Desk Booking — Decisions

> Reconstructed from the flat source spec. Ratify during the v2 rebuild.

## ADR: Dual uniqueness (desk/date AND employee/date)

- **Context:** A desk must not be double-booked, and an employee should hold at most one desk per day.
- **Decision:** Two DB unique indexes — `(desk_id, booking_date)` and `(employee_id, booking_date)` — asserted inside a transaction; violations return friendly messages.
- **Consequences:** Concurrency-safe; a user cannot hoard multiple desks in one day.

## ADR: Advance + consecutive-day limits *(assumed defaults)*

- **Decision:** Max 14 days in advance, max 5 consecutive days, both configurable defaults.
- **Consequences:** Prevents desk squatting; defaults chosen conservatively pending product input.

## ADR: No-show auto-release at 11:00 *(assumed)*

- **Decision:** `ReleaseDeskNoShowsCommand` frees today's `booked` desks with no check-in at 11:00 daily.
- **Consequences:** Recovers unclaimed desks for walk-ins; cutoff is a configurable default.

## ADR: Floor map is positioned divs over a floor image *(assumed)*

- **Decision:** The floor map renders desks as absolutely-positioned elements using `wp_desks.position { x, y }` over an uploaded floor image, rather than a vector CAD renderer.
- **Consequences:** Cheap to build in a Livewire custom page; positions maintained via the DeskResource form.
