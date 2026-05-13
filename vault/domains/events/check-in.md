---
type: module
domain: Events Management
panel: events
module-key: events.checkin
status: planned
color: "#4ADE80"
---

# Check-In

> Event-day check-in — QR code scanning, manual search check-in, real-time attendance count, and attendance report.

**Panel:** `events`
**Module key:** `events.checkin`

---

## What It Does

Check-In provides a purpose-built interface for on-the-day attendee registration. Event staff open the check-in page on a tablet or laptop at the venue and scan each attendee's QR code from their confirmation email. Alternatively, staff can search by name or email for attendees who do not have the QR code. The interface shows a real-time attendance count and allows walk-in attendees (if permitted) to be checked in with ad-hoc registration. After the event, a full attendance report is available showing who attended, who was a no-show, and the final headcount.

---

## Features

### Core
- QR code scanning: scan the attendee's confirmation QR code from the camera on a tablet or phone
- Manual search: search by name or email for attendees without their QR code
- Check-in confirmation: clear visual confirmation (green tick) on successful check-in
- Real-time attendance counter: live total of checked-in attendees vs total registered
- Walk-in registration: add unregistered attendees on the door (if walk-ins are permitted for the event)
- Duplicate prevention: alert if a QR code has already been scanned

### Advanced
- Session-level check-in: for multi-session events, check-in separately for each session
- Badge printing: trigger badge print on check-in for events with physical badges
- Offline mode: check-in continues to work if internet connectivity is lost; syncs on reconnection
- Staff assignment: assign specific staff members to specific check-in stations
- Waitlist promotion: check-in interface shows waitlisted attendees and allows promotion to confirmed on the door

### AI-Powered
- Predicted peak arrival time: forecast the busiest arrival windows for staffing decisions
- No-show prediction: identify registered attendees unlikely to attend based on past behaviour

---

## Data Model

```erDiagram
    check_in_records {
        ulid id PK
        ulid registration_id FK
        ulid event_id FK
        ulid company_id FK
        string method
        ulid checked_in_by FK
        boolean is_walk_in
        timestamp checked_in_at
    }

    check_in_records }o--|| registrations : "confirms"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `check_in_records` | Check-in events | `id`, `registration_id`, `event_id`, `method`, `is_walk_in`, `checked_in_at` |

---

## Permissions

```
events.checkin.use
events.checkin.walk-in-registration
events.checkin.view-report
events.checkin.manage-stations
events.checkin.export
```

---

## Filament

- **Resource:** None (custom page only)
- **Pages:** N/A
- **Custom pages:** `CheckInKioskPage` (full-screen check-in UI), `AttendanceReportPage`
- **Widgets:** `LiveAttendanceWidget`, `CheckInProgressWidget`
- **Nav group:** Events

---

## Displaces

| Feature | FlowFlex | Eventbrite | Cvent | Bizzabo |
|---|---|---|---|---|
| QR code check-in | Yes | Yes | Yes | Yes |
| Offline mode | Yes | Yes | Yes | Yes |
| Real-time attendance count | Yes | Yes | Yes | Yes |
| Session-level check-in | Yes | No | Yes | Yes |
| Included in platform | Yes | No | No | No |

---

## Related

- [[registrations]] — QR codes sourced from registration records
- [[events]] — check-in scoped to a specific event
- [[post-event-analytics]] — check-in data feeds attendance rate calculations
