---
domain: workplace
module: desk-booking
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Desk Booking

Hot-desk reservation for hybrid workplaces. Employees book desks by date; a floor map shows availability.

## Module-key

| Field | Value |
|---|---|
| key | `workplace.desks` |
| priority | p3 |
| panel | workplace |
| permission-prefix | `workplace.desks` |
| tables | `wp_desks`, `wp_desk_bookings` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | bookers are employees |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |

## Core Features

- **Desk catalogue + floor map** — desk records (zone, floor, type, equipment, map position) laid out over a floor image. See [[features/floor-map|Floor Map]].
- **Book a desk** — reserve a desk for a date (full-day v1 *(assumed)*, recurring weekdays); dual uniqueness. See [[features/book-a-desk|Book a Desk]].
- **My bookings + team view** — where teammates sit on a given day. See [[features/team-view|Team View]].
- **Check-in / no-show auto-release** — release unclaimed desks by 11:00 *(assumed)*. See [[features/check-in-release|Check-in & Auto-release]].

## See features/

- [[features/floor-map|Floor Map]] · [[features/book-a-desk|Book a Desk]] · [[features/team-view|Team View]] · [[features/check-in-release|Check-in & Auto-release]]

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Double-booking (desk/date + employee/date) rejected, concurrent-safe.
- [ ] Advance + consecutive-day rules enforced.
- [ ] No-show release at cutoff once.
- [ ] Team view shows same-day colleagues only.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none confirmed)* | — | No cross-domain event specified *(assumed)*; see [[unknowns]]. |
| Reads | employee directory | hr.profiles | bookers + teammates resolved from HR (read-only) |

**Data ownership:** `workplace.desks` writes only `wp_desks` + `wp_desk_bookings`. Bookers + teammates are read from `hr.profiles` (read-only). No other domain's tables are written ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../room-booking/_module|Room Booking]] · [[../workplace-analytics/_module|Workplace Analytics]]
