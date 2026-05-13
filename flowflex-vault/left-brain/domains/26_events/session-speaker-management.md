---
type: module
domain: Events Management
panel: events
phase: 5
status: complete
cssclasses: domain-events
migration_range: 991500–991999
last_updated: 2026-05-12
---

# Session & Speaker Management

Programme management for multi-session events. Speaker profiles, session scheduling, abstract/CFP submissions, and the published event agenda.

---

## Session Structure

```
Event
└── Tracks (parallel streams)
    └── Sessions
        ├── Speakers (one or many)
        ├── Location / Room
        ├── Capacity (for workshops)
        └── Session materials
```

Session types: keynote, panel, workshop, breakout, networking, sponsor slot, Q&A.

---

## Speaker Management

Speaker profile:
- Bio, headshot, company, title
- Social links (LinkedIn, X)
- Talk title + abstract
- Technical requirements (slides format, AV needs)
- Dietary / travel requirements
- Honorarium / travel expense tracking

**Speaker portal**: self-service. Speaker fills in own bio, uploads headshot + slides, confirms session details. No back-and-forth emails.

---

## Call for Papers (CFP)

For conferences:
1. Open CFP with submission form (topic, abstract, speaker bio)
2. Programme committee reviews submissions (internal scoring)
3. Accept / reject / waitlist
4. Accepted speakers invited to create account → speaker portal
5. Session scheduled and published to agenda

---

## Agenda Builder

Drag-and-drop schedule builder:
- Columns = time slots (30-min increments)
- Rows = rooms/tracks
- Drop sessions into slots
- Conflict detection (speaker in two rooms at same time)
- Auto-detect overlap

Published agenda: public-facing page (pulls from event page in [[event-creation-branding]]).

---

## Data Model

### `evt_sessions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| event_id | ulid | FK |
| track | varchar(100) | nullable |
| title | varchar(300) | |
| type | enum | keynote/panel/workshop/breakout/networking |
| starts_at | timestamp | |
| ends_at | timestamp | |
| room | varchar(100) | nullable |
| capacity | int | nullable |
| status | enum | draft/published/cancelled |

### `evt_speakers`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| company | varchar(200) | nullable |
| bio | text | nullable |
| headshot_url | varchar(500) | nullable |
| email | varchar(300) | |

---

## Migration

```
991500_create_evt_sessions_table
991501_create_evt_speakers_table
991502_create_evt_session_speakers_table
991503_create_evt_cfp_submissions_table
```

---

## Related

- [[MOC_Events]]
- [[event-creation-branding]]
- [[attendee-management]]
- [[event-checkin-app]]
