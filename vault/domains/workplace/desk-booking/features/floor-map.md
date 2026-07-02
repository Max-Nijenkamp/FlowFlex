---
domain: workplace
module: desk-booking
feature: floor-map
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Floor Map

Visual desk layout over a floor image, showing availability for a chosen date.

## Behaviour

- Desks render as positioned markers using `wp_desks.position { x, y }` over an uploaded floor image *(assumed)*.
- Marker colour reflects availability for the selected date (free / booked / mine / released).
- Desk CRUD (identifier, zone, floor, type, equipment, position, bookable) via `DeskResource`.

## UI

- **Kind**: custom-page (spatial / floor-map)
- **Page**: `DeskBookingPage` — "Desk Booking" (`/workplace/desks`); desk CRUD on `DeskResource` (`/workplace/desks/manage`).
- **Layout**: floor image with absolutely-positioned desk markers; date picker top bar; legend for status colours; right rail = zone/floor filter.
- **Key interactions**: pick date → map recolours; click a free desk → book modal; polling refresh 60s.
- **States**: empty (no desks / no floor image → "upload a floor plan" CTA) · loading (map skeleton) · error (toast) · selected (desk marker highlighted).
- **Gating**: view `workplace.desks.view-any`; edit desks `workplace.desks.manage`.

## Data

- Owns / writes: `wp_desks` only (positions, attributes).
- Reads: `wp_desk_bookings` (own module) for availability colouring.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: desk inventory + positions read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Availability colour mapping: free / booked / mine / released for a desk on the selected date.

### Feature (Pest)
- [ ] Desks render from `wp_desks.position { x, y }` scoped to the acting company.
- [ ] Selecting a date recomputes each desk's availability state correctly.
- [ ] Desk position/attribute edit persists via `DeskResource`.

### Livewire
- [ ] Empty state (no desks / no floor image) shows the "upload a floor plan" CTA.
- [ ] View gated on `workplace.desks.view-any`; desk edit on `workplace.desks.manage`.

## Related

- [[../_module|Desk Booking]] · [[book-a-desk]] · [[../data-model]]
