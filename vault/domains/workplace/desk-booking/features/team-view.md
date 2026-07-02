---
domain: workplace
module: desk-booking
feature: team-view
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Team View

See where teammates are sitting on a given day, plus a "my bookings" list.

## Behaviour

- "My bookings" lists the acting employee's upcoming desk bookings.
- Team view lists colleagues with a desk booked for the selected day (same-day bookings only), optionally highlighted on the floor map.
- Scoped to the acting company; shows same-day bookings, not history.

## UI

- **Kind**: custom-page panel (tab on the floor map page)
- **Page**: "Team" + "My bookings" tabs on `DeskBookingPage`.
- **Layout**: list of colleagues + their desk/zone for the day; clicking a colleague highlights their desk marker on the map.
- **Key interactions**: switch date → list + markers update; click colleague → map focus.
- **States**: empty (no one booked that day → "no colleagues in yet" message) · loading (list skeleton) · error (toast) · selected (colleague row + marker highlighted).
- **Gating**: `workplace.desks.view-any`.

## Data

- Owns / writes: nothing (read-only view over `wp_desk_bookings`).
- Reads: `wp_desk_bookings` + `wp_desks` (own module); `hr.profiles` for colleague names/avatars (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: `hr_employees` — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

> [!warning] UNVERIFIED
> Whether an employee can opt out of appearing in the team view (privacy) is undecided — see [[../unknowns]].

## Related

- [[../_module|Desk Booking]] · [[floor-map]] · [[../security]]
