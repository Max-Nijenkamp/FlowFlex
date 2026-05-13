---
type: module
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
status: complete
migration_range: 855000–859999
last_updated: 2026-05-12
---

# Meeting Room Management

Book meeting rooms, manage AV resources, track utilisation, and integrate with calendar systems. Display panels show live room status.

---

## Core Functionality

### Room Catalogue
- Rooms defined per building/floor with capacity, AV equipment list, and photos
- Room features: video conferencing (Teams/Zoom/Google Meet hardware), whiteboard, projector, catering service available
- Accessibility flags (wheelchair, hearing loop)
- Cost-per-hour (for internal chargeback to departments)

### Booking
- Search by capacity, date/time, features, building
- Reserve from Filament panel or calendar integration (Outlook/Google)
- Add services at booking time: catering order, AV tech support request
- Invite attendees — sends calendar invite with room details
- Recurring meeting support (weekly standup, fortnightly all-hands)
- Instant release: no-show after 15 minutes → room released automatically

### Display Panel Integration
- Room display panels (e.g., Logitech Tap, Crestron) pull status via public API endpoint
- Endpoint: `GET /api/workplace/rooms/{id}/status` — returns current/next booking + available until
- Display shows: room name, current booking (title, organiser, end time), next booking, available/occupied badge
- Walk-up ad-hoc booking from panel via PIN or NFC

### Room Booking Rules
- Max advance booking: configurable per room type (e.g., board room = 90 days, hot room = 7 days)
- Max duration per booking (e.g., 4 hours default, 8 hours max with approval)
- No back-to-back same organiser without 15-min gap (cleaning buffer)
- Approval required for large rooms > 20 people capacity

---

## Data Model

### `workplace_rooms`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| floor_id | ulid | FK |
| name | varchar(100) | "Boardroom A" |
| capacity | int | |
| features | json | ["video_conf","whiteboard","projector"] |
| display_pin | varchar(6) | For walk-up panel booking |
| hourly_rate | decimal(10,2) | nullable, for chargebacks |
| requires_approval | bool | |
| active | bool | |

### `workplace_room_bookings`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| room_id | ulid | FK |
| organiser_id | ulid | FK `employees` |
| title | varchar(200) | Meeting name |
| starts_at | datetime | |
| ends_at | datetime | |
| attendee_count | int | |
| checked_in_at | timestamp | nullable |
| status | enum | confirmed/checked_in/no_show/cancelled |
| catering_notes | text | nullable |
| external_calendar_event_id | varchar | nullable |

---

## Utilisation Tracking

Feeds into [[workplace-analytics]]:
- Booked hours vs available hours per room per day
- No-show rate per room (reveals over-demanded rooms)
- Average attendee count vs room capacity (right-sizing recommendations)
- Peak booking hours heat-map

---

## Integrations

- **Microsoft Graph API** — two-way sync for Outlook room calendars
- **Google Calendar API** — two-way sync for Google Workspace room resources
- **Catering module** (if Operations catering enabled) — auto-create catering order
- **Communications** — room booking = calendar event for all attendees

---

## Migration

```
855000_create_workplace_rooms_table
855001_create_workplace_room_bookings_table
855002_create_workplace_room_services_table
```

---

## Related

- [[MOC_Workplace]]
- [[hot-desk-space-booking]]
- [[workplace-analytics]]
- [[MOC_Communications]] — calendar sync
