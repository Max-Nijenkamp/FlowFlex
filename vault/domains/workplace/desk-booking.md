---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.desks
status: planned
color: "#4ADE80"
---

# Desk Booking

Hot-desk reservation for hybrid workplaces. Employees book desks by date; floor map shows availability.

## Core Features

- Desk record: identifier, zone/floor, type (standing/sitting), equipment
- Floor map: visual desk layout with availability
- Book a desk for a date (or recurring days)
- My bookings view
- Team booking: see where teammates are sitting
- Check-in / auto-release no-shows
- Desk utilisation reporting
- Booking rules: max days in advance, max consecutive days

## Data Model

| Table | Key Columns |
|---|---|
| `wp_desks` | company_id, identifier, zone, floor, type, equipment (json), position (json for map) |
| `wp_desk_bookings` | company_id, desk_id, employee_id, booking_date, status, checked_in_at |

## Filament

**Nav group:** Desks

- `DeskResource` — manage desks + map positions
- `DeskBookingPage` (custom page) — floor map with availability, book by date

## Related

- [[domains/workplace/room-booking]]
- [[domains/hr/employee-profiles]]
