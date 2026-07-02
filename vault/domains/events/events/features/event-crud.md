---
domain: events
module: events
feature: event-crud
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Event CRUD & Lifecycle

Create, edit, publish, and cancel events; the scheduled lifecycle command advances them through the state machine.

## Behaviour

- Create a `draft` event (name, type, venue, dates, capacity, virtual link).
- `publish` transitions `draft → published` — the landing page goes live and registration opens.
- `EventLifecycleCommand` (every 15 min) advances `published → live` at `start_at` and `live → completed` at `end_at`, guarded on status + time.
- `cancel` transitions `draft`/`published` → `cancelled` and notifies registrants (same-domain call into registrations).
- `end_at` must be after `start_at`; both validated on create/edit.

## UI

- **Kind**: simple-resource
- **Page**: `EventResource` list + form at `/app/events/events`.
- **Layout**: table (name, type badge, start date, status badge, registration count); tabbed/section form (details · schedule · capacity · virtual). Sessions relation manager on the edit page.
- **Key interactions**: `Publish` and `Cancel` header/row actions (gated + confirmed); status badge reflects state machine; capacity field toggles unlimited.
- **States**: empty (no events → "create your first event" CTA) · loading (skeleton table) · error (validation toast; publish blocked with reason) · selected (row → view/edit).
- **Gating**: `events.events.view-any` to see; `events.events.create`/`update`; `Publish` needs `events.events.publish`; `Cancel` needs `events.events.cancel`.

## Data

- Owns / writes: `ev_events` only (and `ev_sessions` via the agenda feature).
- Reads: `ev_venues` + rooms via the Venues service (soft dep).
- Cross-domain writes: NONE. Cancel notification into registrations is a **same-domain** service call; no other domain's tables are written ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: a published event is the anchor consumed (read) by registrations, speakers, sponsors, analytics.
- Shared entity: `ev_venues` (owned by [[../../venues/_module|Venues]]), read via its service.

## Unknowns

- Whether lifecycle transitions should fire domain events for reminders/analytics *(assumed: none)* — see [[../unknowns]].

## Related

- [[../_module|Events]] · [[agenda-sessions]] · [[public-landing]] · [[../architecture]]
