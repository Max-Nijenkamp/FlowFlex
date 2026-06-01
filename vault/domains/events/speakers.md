---
type: module
domain: Events Management
panel: events
module-key: events.speakers
status: planned
color: "#4ADE80"
---

# Speakers

Manage event speakers: profiles, session assignments, and speaker logistics.

## Core Features

- Speaker record: name, bio, photo, title, company, social links
- Assign speakers to event sessions
- Speaker logistics: travel, accommodation, AV requirements, notes
- Speaker confirmation status (invited/confirmed/declined)
- Public speaker profile on event landing page
- Speaker portal link (submit bio/slides)
- Reusable speaker directory across events

## Data Model

| Table | Key Columns |
|---|---|
| `ev_speakers` | company_id, name, bio, photo_media_id, title, company_name, social_links (json) |
| `ev_session_speakers` | session_id, speaker_id, company_id, confirmation_status |

## Filament

**Nav group:** Speakers

- `SpeakerResource` — manage speaker directory
- Assign to sessions via Event relation manager

## Related

- [[domains/events/events]]
- [[domains/events/sponsors]]
