---
tags: [flowflex, domain/communications, overview, phase/5]
domain: Communications & Internal Comms
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-07
---

# Communications Overview

Internal messaging, announcements, video meetings, company intranet, and appointment booking. All 5 modules built in Phase 5 as a complete panel.

**Filament Panel:** `communications`
**Domain Colour:** Sky `#0284C7` / Light: `#E0F2FE`
**Domain Icon:** `heroicon-o-chat-bubble-left-right`
**Phase:** 5 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[Internal Messaging & Chat]] | Channels (public/private/DM), messages, threads, reactions, file attachments |
| [[Company Announcements]] | Company-wide broadcasts, read receipts, acknowledgement tracking |
| [[Meeting & Video Integration]] | Meeting scheduling, Google Meet/Zoom/Teams links, notes, action items |
| [[Company Intranet]] | Company pages, news feed, org chart, pinned content, search |
| [[Booking & Appointment Scheduling]] | Booking pages, availability, appointments, confirmation emails, calendar sync |

## Filament Panel Structure

**Navigation Groups:**
- `Messaging` — Channels, Direct Messages
- `Broadcast` — Announcements
- `Meetings` — Meetings, Meeting Notes, Action Items
- `Intranet` — Intranet Pages, News, Org Chart
- `Bookings` — Booking Pages, Appointments

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `AnnouncementPublished` | Announcements | Push notification to all tenants |
| `MessageSent` | Messaging | Real-time push via Pusher |
| `ChannelCreated` | Messaging | Notifications (invited members) |
| `MeetingScheduled` | Meetings | Email (invites to attendees), Calendar (add event) |
| `MeetingCompleted` | Meetings | Tasks (auto-create action items from meeting notes) |
| `AppointmentBooked` | Booking | Email (confirmation + calendar invite to both parties) |
| `AppointmentCancelled` | Booking | Email (notify host and attendee) |
| `EmployeeProfileCreated` | HR (Phase 2) | Intranet (auto-add to org chart) |

## Real-Time Requirements

Internal Messaging uses **Pusher** (configured in Phase 1) for real-time message delivery. All other modules are standard request/response.

## Permissions Prefix

`communications.messaging.*` · `communications.announcements.*` · `communications.meetings.*`  
`communications.intranet.*` · `communications.bookings.*`

## Database Migration Range

`450000–499999`

## Related

- [[Internal Messaging & Chat]]
- [[Company Announcements]]
- [[Meeting & Video Integration]]
- [[Company Intranet]]
- [[Booking & Appointment Scheduling]]
- [[Panel Map]]
- [[Build Order (Phases)]]
