---
type: module
domain: Workplace & Facility
domain-key: workplace
panel: workplace
module-key: workplace.desks
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [wp_desks, wp_desk_bookings]
permission-prefix: workplace.desks
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Desk Booking

Hot-desk reservation for hybrid workplaces. Employees book desks by date; floor map shows availability.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | bookers are employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Desk record: identifier, zone/floor, type (standing/sitting), equipment, map position
- Floor map: visual desk layout with availability (positioned divs over floor image *(assumed)*)
- Book a desk for a date (full-day v1 *(assumed)*; recurring weekdays)
- One desk per employee per date; one booking per desk per date
- My bookings view; team view (where teammates sit — same-day bookings list)
- Check-in / auto-release no-shows (by 11:00 *(assumed)*)
- Desk utilisation reporting (analytics)
- Booking rules: max days in advance (default 14), max consecutive days (default 5) *(assumed defaults, configurable)*

---

## Data Model

### wp_desks — id, company_id (indexed), identifier (unique per company), zone, floor, type, equipment (jsonb), position (jsonb x/y), is_bookable, deleted_at
### wp_desk_bookings

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), desk_id FK, employee_id FK | ulid | |
| booking_date | date | unique `(desk_id, booking_date)` AND unique `(employee_id, booking_date)` |
| status | string default `booked` | booked / cancelled / released |
| checked_in_at | timestamp nullable | |

---

## DTOs

### BookDeskData — desk_id (bookable, free that date), booking_date (future, ≤ max advance), recurrence weekdays+until? — rule violations with messages ("You already have a desk booked for this date.")

## Services & Actions

- `DeskBookingService::book(...)` — both uniqueness rules in transaction; advance/consecutive rule checks
- `CheckInDeskAction` / `ReleaseDeskNoShowsCommand` (daily 11:00)

---

## Filament

**Nav group:** Desks

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DeskResource` | #1 CRUD resource | map position fields |
| `DeskBookingPage` | #11-style map custom page | floor map, date picker, click-to-book, team view; polling 60s |

---

## Permissions

`workplace.desks.book` (all users) · `workplace.desks.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Double-booking (desk/date + employee/date) rejected, concurrent-safe
- [ ] Advance + consecutive-day rules enforced
- [ ] No-show release at cutoff once
- [ ] Team view shows same-day colleagues only

---

## Build Manifest

```
database/migrations/xxxx_create_wp_desks_table.php
database/migrations/xxxx_create_wp_desk_bookings_table.php
app/Models/Workplace/{Desk,DeskBooking}.php
app/Data/Workplace/BookDeskData.php
app/Services/Workplace/DeskBookingService.php
app/Actions/Workplace/CheckInDeskAction.php
app/Console/Commands/Workplace/ReleaseDeskNoShowsCommand.php
app/Filament/Workplace/Resources/DeskResource.php
app/Filament/Workplace/Pages/DeskBookingPage.php
database/factories/Workplace/{DeskFactory,DeskBookingFactory}.php
tests/Feature/Workplace/DeskBookingTest.php
```

---

## Related

- [[domains/workplace/room-booking]]
- [[domains/hr/employee-profiles]]
