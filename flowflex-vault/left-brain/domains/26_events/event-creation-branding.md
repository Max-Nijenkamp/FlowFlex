---
type: module
domain: Events Management
panel: events
phase: 5
status: complete
cssclasses: domain-events
migration_range: 990000–990499
last_updated: 2026-05-12
---

# Event Creation & Branding

Create and configure events — physical, virtual, or hybrid. Custom branded registration pages, event websites, and email communications without needing design or dev resources.

---

## Event Types

| Type | Description |
|---|---|
| Conference | Multi-day, multi-track, 50–5,000+ attendees |
| Webinar | Online-only, 30 min–2 hrs |
| Workshop | Small group, hands-on, limited seats |
| Networking | Open registration, drop-in |
| Internal | Team events, all-hands, training |
| Hybrid | In-person venue + live stream |

---

## Event Setup

Core configuration:
- Title, description, category/tags
- Date(s) and times (multi-day support)
- Venue (physical address + map embed) or streaming platform link
- Capacity limits (per ticket type)
- Timezone (auto-convert for international attendees)
- Visibility: public / invite-only / unlisted

---

## Branded Event Page

Drag-and-drop event page builder:
- Logo, banner image, colour scheme (inherits brand kit or custom)
- Schedule overview (pulls from [[session-speaker-management]])
- Speaker profiles
- Sponsor logos and tiers
- FAQ section
- Countdown timer
- Social sharing meta tags (OG image auto-generated)

Custom domain: `events.company.com/event-slug`

---

## Multi-Language

Event page and emails available in multiple languages. Attendees see content in their browser language (if translated). Manual translations via CMS or AI-assisted.

---

## Data Model

### `evt_events`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| slug | varchar(100) | unique per tenant |
| title | varchar(300) | |
| type | enum | conference/webinar/workshop/networking/internal/hybrid |
| starts_at | timestamp | |
| ends_at | timestamp | |
| timezone | varchar(50) | |
| venue_name | varchar(300) | nullable |
| venue_address | text | nullable |
| stream_url | varchar(500) | nullable |
| capacity | int | nullable |
| status | enum | draft/published/live/completed/cancelled |
| branding | json | colours, logo, banner |

---

## Migration

```
990000_create_evt_events_table
990001_create_evt_event_pages_table
990002_create_evt_event_sponsors_table
```

---

## Related

- [[MOC_Events]]
- [[registration-ticketing]]
- [[session-speaker-management]]
- [[attendee-management]]
