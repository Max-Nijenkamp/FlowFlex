---
type: module
domain: Community & Social
panel: community
module-key: community.events
status: planned
color: "#4ADE80"
---

# Events Calendar

> Community events with online and in-person options, RSVP management, a calendar view, and automated reminders.

**Panel:** `community`
**Module key:** `community.events`

---

## What It Does

Events Calendar enables community managers to publish online and in-person events — webinars, meetups, AMAs, workshops — that community members can discover, RSVP to, and add to their personal calendars. Each event has a capacity limit, an optional cost, and a description with rich text and cover image. Automated reminders go out 24 hours before the event. Post-event, attendance is recorded and the recording or recap can be published back to the event page.

---

## Features

### Core
- Event creation: name, date, time, timezone, description, cover image, event type (online/in-person/hybrid)
- Location fields: virtual link (Zoom, Meet, Teams) or physical address
- Capacity and RSVP: set maximum attendees; members RSVP and are waitlisted when full
- Calendar view: monthly and list calendar of upcoming events for members
- Reminder notifications: automated 24-hour and 1-hour reminders to confirmed attendees
- Post-event recap: publish recording link or written recap on the event page

### Advanced
- Recurring events: define a repeat schedule (weekly, monthly, first Monday of the month)
- Ticketing and pricing: free or paid ticket types with Stripe payment integration
- Co-hosts: assign multiple community managers or speakers to an event
- Tag filtering: members filter the events calendar by tag (e.g. webinar, workshop, social)
- Attendance recording: mark actual attendance after the event for analytics

### AI-Powered
- Event summary generation: AI drafts the event description from a brief input
- Attendance prediction: estimate RSVP-to-attendance conversion based on past event data
- Content recommendation: surface related forum threads or resources alongside event details

---

## Data Model

```erDiagram
    community_events {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string event_type
        string virtual_link
        string physical_address
        datetime starts_at
        datetime ends_at
        string timezone
        integer capacity
        boolean is_paid
        decimal ticket_price
        json tags
        string status
        timestamps created_at_updated_at
    }

    event_rsvps {
        ulid id PK
        ulid event_id FK
        ulid member_id FK
        string status
        boolean attended
        timestamp rsvped_at
    }

    community_events ||--o{ event_rsvps : "receives"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `community_events` | Event definitions | `id`, `company_id`, `name`, `starts_at`, `capacity`, `event_type`, `status` |
| `event_rsvps` | Attendance records | `id`, `event_id`, `member_id`, `status`, `attended` |

---

## Permissions

```
community.events.view
community.events.create
community.events.update
community.events.delete
community.events.manage-rsvps
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\CommunityEventResource`
- **Pages:** `ListCommunityEvents`, `CreateCommunityEvent`, `EditCommunityEvent`, `ViewCommunityEvent`
- **Custom pages:** `EventCalendarPage` (member-facing calendar), `EventAttendeePage`
- **Widgets:** `UpcomingEventsWidget`, `RsvpConversionWidget`
- **Nav group:** Events

---

## Displaces

| Feature | FlowFlex | Circle.so | Luma | Eventbrite |
|---|---|---|---|---|
| Community-integrated events | Yes | Yes | No | No |
| RSVP + waitlist | Yes | Yes | Yes | Yes |
| Paid ticketing | Yes | Yes | Yes | Yes |
| Native forum + events | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[forums]] — event-related forum threads
- [[member-profiles]] — attended events shown on profile
- [[badges]] — event attendance can trigger badge awards
- [[events/INDEX]] — corporate events management domain (separate)
