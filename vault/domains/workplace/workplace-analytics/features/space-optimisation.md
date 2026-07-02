---
domain: workplace
module: workplace-analytics
feature: space-optimisation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Space Optimisation

Surface underused rooms and desks to inform facility planning decisions.

## Behaviour

- Lists rooms/desks whose utilisation falls below a threshold over the selected range.
- Threshold = fixed or configurable *(assumed)* — see [[../unknowns]].
- Read-only insight; no action taken automatically.

## UI

- **Kind**: widget (list fragment on the dashboard)
- **Page**: "Underused space" widget on `WorkplaceDashboardPage`.
- **Layout**: ranked list (room/desk, utilisation %, trend arrow); links back to the resource.
- **Key interactions**: click item → open the room/desk record; adjust range → list recomputes.
- **States**: empty (nothing under threshold → "space is well used") · loading (skeleton) · error (toast) · selected (item highlighted).
- **Gating**: `workplace.analytics.view-any`.

## Data

- Owns / writes: nothing.
- Reads: `wp_rooms` + `wp_room_bookings`, `wp_desks` + `wp_desk_bookings` via the owning modules (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: utilisation from room + desk modules (read-only).
- Feeds: nothing.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Threshold comparison flags only rooms/desks below utilisation for the range

### Feature (Pest)
- [ ] Ranked list reads via owning modules' read models — company-scoped

### Livewire
- [ ] "Space is well used" empty state when nothing under threshold; item links to the room/desk record

## Related

- [[../_module|Workplace Analytics]] · [[utilisation-dashboard]] · [[../unknowns]]
