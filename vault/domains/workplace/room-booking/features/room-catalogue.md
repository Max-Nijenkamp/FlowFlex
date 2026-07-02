---
domain: workplace
module: room-booking
feature: room-catalogue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Room Catalogue

Manage the set of bookable meeting rooms — name, location, capacity, amenities, bookable toggle.

## Behaviour

- CRUD a room: name (unique per company), location/floor, capacity, amenities (projector / whiteboard / video), `is_bookable`.
- Toggling `is_bookable = false` hides a room from the booking calendar without deleting history.
- Rooms are filterable by capacity and amenities on the booking page.

## UI

- **Kind**: simple-resource
- **Page**: `RoomResource` list/form at `/workplace/rooms`.
- **Layout**: table (name, location, capacity, amenities badges, bookable toggle); section form with amenity checkboxes.
- **Key interactions**: create/edit room; toggle bookable inline.
- **States**: empty (no rooms → "add your first room" CTA) · loading (table skeleton) · error (toast) · selected (row → edit).
- **Gating**: view `workplace.rooms.view-any`; create/edit `workplace.rooms.manage`.

## Data

- Owns / writes: `wp_rooms` only.
- Reads: nothing cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: room records read by [[../../workplace-analytics/_module|Workplace Analytics]] (utilisation).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Room name unique per company; amenity set validates against the allowed list.

### Feature (Pest)
- [ ] Create / edit a room persists amenities + capacity.
- [ ] `is_bookable = false` removes the room from the booking calendar without deleting history.
- [ ] Optimistic stale-check: a second editor saving a room changed underneath them gets the conflict notification ([[../architecture#Concurrency]]).

### Livewire
- [ ] `RoomResource` table renders amenity badges + bookable toggle; inline toggle persists.
- [ ] Create/edit gated on `workplace.rooms.manage`; view on `workplace.rooms.view-any`.

## Related

- [[../_module|Room Booking]] · [[book-a-room]] · [[../data-model]]
