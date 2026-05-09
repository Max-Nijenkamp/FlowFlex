---
tags: [flowflex, domain/community, events, meetups, phase/7]
domain: Community & Social
panel: community
color: "#E11D48"
status: planned
last_updated: 2026-05-08
---

# Events & Meetups

Community events: virtual webinars, in-person meetups, AMA sessions, workshops. RSVP, reminders, and recordings — all in one place.

**Who uses it:** Community admins, members
**Filament Panel:** `community`
**Depends on:** [[Member Directory & Profiles]], [[Booking & Appointment Scheduling]] (for calendar sync), [[Meeting & Video Integration]]
**Phase:** 7

---

## Features

### Event Creation

- Event types: webinar, in-person meetup, AMA (Ask Me Anything), workshop, conference, watch party
- Cover image, title, description (rich text), tags
- Date/time with timezone support
- Location: virtual (meeting link) / in-person (address + map embed) / hybrid
- Capacity limit with waitlist
- Ticket types: free / paid / members-only / invite-only
- Co-host assignment (multiple hosts)
- Repeat events (weekly, monthly)

### RSVP & Attendance

- One-click RSVP from event page
- Calendar invite auto-sent to attendees (iCal format)
- Reminder emails: 1 week before, 1 day before, 1 hour before
- Check-in: QR code check-in for in-person events
- Attendance tracking: confirmed vs actual attended
- Waitlist management: auto-promote when spot opens

### During the Event

- Virtual events: embedded meeting link (Zoom, Google Meet, or native video — [[Meeting & Video Integration]])
- Live Q&A: members submit questions, host marks answered
- Polls: launch live polls, see real-time results
- Reactions: thumbs-up, hands, fire — visible to presenter
- Recording consent prompt at event start

### Post-Event

- Automatic recording upload to event page (if native video used)
- Recording available to: all members / attendees only / admin only
- Post-event survey (link to [[Forms & Lead Capture]])
- Thank-you email to attendees with recording link
- Summary post auto-drafted by AI ("Recap: 3 key takeaways from yesterday's AMA")
- Attendee community points awarded automatically

### Event Discovery

- Community events calendar (month/week view)
- Featured events on community homepage
- Events filtered by type, Space, upcoming vs past
- "Events you might like" — AI recommendations based on member interests

---

## Database Tables (3)

### `community_events`
| Column | Type | Notes |
|---|---|---|
| `space_id` | ulid FK nullable | optional Space association |
| `host_id` | ulid FK | → community_members |
| `type` | enum | `webinar`, `meetup`, `ama`, `workshop` |
| `title` | string | |
| `description` | json | rich text |
| `cover_file_id` | ulid FK nullable | |
| `starts_at` | timestamp | |
| `ends_at` | timestamp | |
| `timezone` | string | |
| `location_type` | enum | `virtual`, `in_person`, `hybrid` |
| `location_url` | string nullable | meeting link |
| `location_address` | text nullable | |
| `capacity` | integer nullable | |
| `waitlist_enabled` | boolean | |
| `visibility` | enum | `public`, `members`, `invite` |
| `status` | enum | `draft`, `published`, `live`, `ended`, `cancelled` |
| `recording_url` | string nullable | |
| `rsvp_count` | integer | cached |

### `community_event_rsvps`
| Column | Type | Notes |
|---|---|---|
| `event_id` | ulid FK | |
| `member_id` | ulid FK | |
| `status` | enum | `going`, `waitlist`, `not_going` |
| `attended` | boolean | |
| `checked_in_at` | timestamp nullable | |

### `community_event_hosts`
| Column | Type | Notes |
|---|---|---|
| `event_id` | ulid FK | |
| `member_id` | ulid FK | |
| `role` | enum | `host`, `co_host`, `speaker` |

---

## Related

- [[Community Overview]]
- [[Booking & Appointment Scheduling]]
- [[Meeting & Video Integration]]
- [[Gamification & Reputation]]
