---
type: domain-index
domain: Workplace & Facility
panel: workplace
color: "#4ADE80"
---

# Workplace & Facility

Room booking, desk booking, visitor management, facility maintenance, and analytics. **Panel:** `/workplace` (Lime) — Phase 3.

---

## Navigation Groups

- **Meeting Rooms** — Rooms, Room Booking
- **Desks** — Desks, Desk Booking
- **Visitors** — Visitor Log, Kiosk
- **Maintenance** — Requests, Schedules
- **Analytics** — Workplace Dashboard

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/workplace/room-booking\|Room Booking]] | `workplace.rooms` | planned | **P3 core** |
| [[domains/workplace/desk-booking\|Desk Booking]] | `workplace.desks` | planned | P3 |
| [[domains/workplace/visitor-management\|Visitor Management]] | `workplace.visitors` | planned | P3 |
| [[domains/workplace/maintenance\|Facility Maintenance]] | `workplace.maintenance` | planned | P3 |
| [[domains/workplace/workplace-analytics\|Workplace Analytics]] | `workplace.analytics` | planned | P3 |

---

## Key Patterns

- `saade/filament-fullcalendar` — room + desk booking calendars
- Custom pages — booking calendar, floor map, kiosk
- `spatie/laravel-model-states` — maintenance request status
- Host/visitor notifications via Core Notifications + email
- Integrates with [[domains/hr/employee-profiles]]
