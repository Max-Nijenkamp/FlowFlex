---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.speakers
status: planned
priority: p3
depends-on: [events.events, core.billing, core.rbac, core.files]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [ev_speakers, ev_session_speakers]
permission-prefix: events.speakers
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Speakers

Manage event speakers: profiles, session assignments, and speaker logistics.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/events/events\|events.events]] | speakers assigned to sessions |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, photos |

---

## Core Features

- Speaker record: name, bio, photo, title, company, social links — reusable directory across events
- Assign speakers to event sessions
- Speaker logistics: travel, accommodation, AV requirements, notes (internal)
- Speaker confirmation status (invited/confirmed/declined) per session
- Public speaker profile on event landing page (confirmed only)
- Speaker submission link: signed token to submit/update bio + photo *(assumed: replaces "portal")*

---

## Data Model

### ev_speakers — id, company_id (indexed), name, bio (purified), photo_media_id nullable, title, company_name, social_links (jsonb), logistics (jsonb internal), submit_token uuid unique, deleted_at
### ev_session_speakers — id, session_id FK, speaker_id FK, company_id, confirmation_status (invited/confirmed/declined); unique `(session_id, speaker_id)`

---

## DTOs

### CreateSpeakerData — name (required), bio?, title?, company_name?, social_links{}
### SpeakerSubmitData (public token) — bio, photo — rate-limited

## Services & Actions

- `AssignSpeakerAction::run(sessionId, speakerId)` — invited status, notification mail *(assumed)*
- `ConfirmSpeakerAction` (via mail link or admin)
- Public submit controller (token)

---

## Filament

**Nav group:** Speakers

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SpeakerResource` | #1 CRUD resource | directory; submit-link copy |
| Session assignment | relation on EventResource sessions | confirmation badges |

Public profiles on event landing (confirmed only).

---

## Permissions

`events.speakers.view-any` · `events.speakers.manage` · `events.speakers.assign`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Landing shows confirmed speakers only; logistics never public
- [ ] Duplicate session assignment rejected
- [ ] Token submit updates bio/photo; invalid token 404
- [ ] Bio purified

---

## Build Manifest

```
database/migrations/xxxx_create_ev_speakers_table.php
database/migrations/xxxx_create_ev_session_speakers_table.php
app/Models/Events/{Speaker,SessionSpeaker}.php
app/Data/Events/{CreateSpeakerData,SpeakerSubmitData}.php
app/Actions/Events/{AssignSpeakerAction,ConfirmSpeakerAction}.php
app/Http/Controllers/SpeakerSubmitController.php + resources/js/Pages/Events/SpeakerSubmit.vue
app/Filament/Events/Resources/SpeakerResource.php
database/factories/Events/SpeakerFactory.php
tests/Feature/Events/SpeakerTest.php
```

---

## Related

- [[domains/events/events]]
- [[domains/events/sponsors]]
