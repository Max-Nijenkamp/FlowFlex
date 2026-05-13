---
type: module
domain: Events Management
panel: events
phase: 5
status: complete
cssclasses: domain-events
migration_range: 992000–992499
last_updated: 2026-05-12
---

# Event Check-In App

Mobile-first check-in for event staff. QR scan or name search. Handles badge printing, session access control, real-time attendance dashboard.

---

## Check-In Flow

1. Attendee arrives, presents e-ticket (QR code) on phone or printed
2. Staff opens check-in app on tablet/phone, scans QR
3. App shows: name, ticket type, photo (if available), any special notes
4. Staff taps "Check In" → attendee marked as attended
5. Badge printed (if badge printer connected)

**Duplicate check-in protection**: second scan shows "Already checked in at 09:42" + option to override.

---

## Name Search

For attendees without QR (printed ticket lost, phone dead):
- Search by last name or email
- Shows matching records with ticket type
- Check in from search result

---

## Badge Printing

Integration with standard badge printers (Brother, Zebra, Dymo):
- Badge template configured per event
- Prints: name, company, ticket type (colour-coded), optional session schedule
- Reprint option for damaged badges

---

## Session Access Control

For workshop/limited-capacity sessions:
- Each session has its own QR check-in
- Only registered attendees admitted
- Over-capacity alert when room fills
- Walk-in list for available seats

---

## Real-Time Dashboard

Web dashboard for event manager (not just mobile):
- Total checked in vs registered (live count)
- Check-in rate over time (arrival curve — good for predicting peak)
- Breakdown by ticket type
- No-shows list (not checked in with 1 hour to go → send SMS reminder)
- Room-by-room capacity for parallel sessions

---

## Offline Mode

Check-in app works offline (cached attendee list):
- Syncs to server when connection restored
- Prevents "no internet at venue" failure

---

## Data Model

### `evt_checkin_events`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| registration_id | ulid | FK |
| session_id | ulid | nullable FK |
| checked_in_at | timestamp | |
| checked_in_by | ulid | FK staff |
| device_id | varchar(100) | |
| overridden | boolean | false |

---

## Migration

```
992000_create_evt_checkin_events_table
992001_create_evt_checkin_devices_table
```

---

## Related

- [[MOC_Events]]
- [[registration-ticketing]]
- [[attendee-management]]
- [[session-speaker-management]]
- [[post-event-analytics]]
