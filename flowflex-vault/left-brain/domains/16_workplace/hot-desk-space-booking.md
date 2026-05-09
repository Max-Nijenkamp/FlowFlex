---
type: module
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
status: planned
migration_range: 850000–854999
last_updated: 2026-05-09
---

# Hot Desk & Space Booking

Interactive floor plan with desk reservation, neighbourhood assignment, and check-in enforcement. Enables hybrid teams to coordinate who is in the office on which day.

---

## Core Functionality

### Floor Plan Management
- Upload SVG or image-based floor plans per building/floor
- Map desk polygons to desk records (click to book)
- Colour coding: available (green), booked (red), your booking (blue), permanent-assigned (grey)
- Zoom and pan, mobile-optimised view

### Desk Types
- **Hot desk** — first-come book up to N days ahead
- **Neighbourhood** — reserved for a team, bookable by members only
- **Permanent assigned** — locked to specific employee, not bookable
- **Accessible** — flagged for reduced-mobility users, prioritised in search

### Booking Rules
- Max advance booking window (per desk type, e.g., 14 days)
- Max consecutive days per week (enforce hybrid policy: "in at most 3 days/week")
- Auto-release if check-in not completed by 10:00
- Recurring booking (e.g., every Monday for 4 weeks)
- Buddy booking — book desk next to a named colleague

### Check-In Methods
- QR code on desk → scan on phone to check in
- NFC tag tap (on-premise)
- Kiosk app check-in at building entrance
- Auto-check-in via office WiFi or BLE beacon (optional)

---

## Data Model

### `workplace_desks`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| floor_id | ulid | FK `workplace_floors` |
| desk_code | varchar(20) | Human label e.g. "A1-042" |
| type | enum | hot/neighbourhood/permanent/accessible |
| neighbourhood_id | ulid | nullable |
| assigned_employee_id | ulid | nullable, FK `employees` |
| floor_plan_x | float | SVG coordinate |
| floor_plan_y | float | SVG coordinate |
| amenities | json | ["monitor","sit-stand","window"] |
| active | bool | |

### `workplace_desk_bookings`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| desk_id | ulid | FK |
| employee_id | ulid | FK |
| booking_date | date | |
| checked_in_at | timestamp | null if no-show |
| released_at | timestamp | null or auto-release time |
| status | enum | pending/confirmed/checked_in/no_show/cancelled |
| notes | text | nullable |

---

## Business Rules

- No double-booking: unique constraint on `(desk_id, booking_date)` for non-permanent desks
- No-show release: scheduled job at `T+60min` post booking window, sets status=no_show and releases desk for same-day rebooking
- Employee can see their own upcoming bookings in ESS Portal
- Managers can see team's office attendance calendar

---

## Notifications

| Trigger | Channel | Recipient |
|---|---|---|
| Booking confirmed | Email + push | Employee |
| 1 hour before booking | Push | Employee (reminder) |
| No-show released | Push | Employee |
| Desk released — available | Push | Waitlisted employees |

---

## Integrations

- **HR** — employee directory, team membership, hybrid policy per contract type
- **Communications** — "who's in office today" status in messaging
- **Outlook / Google Calendar** — mark office days on personal calendar

---

## Migration

```
850000_create_workplace_floors_table
850001_create_workplace_neighbourhoods_table
850002_create_workplace_desks_table
850003_create_workplace_desk_bookings_table
850004_create_workplace_booking_rules_table
```

---

## Related

- [[MOC_Workplace]]
- [[meeting-room-management]]
- [[office-resource-management]]
- [[workplace-analytics]]
- [[MOC_HR]] — hybrid attendance tracking
