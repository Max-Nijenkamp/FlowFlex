---
domain: events
module: speakers
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers

A reusable speaker directory: profiles, session assignments, logistics, and a public bio-submission link.

## Module-key

| Field | Value |
|---|---|
| key | `events.speakers` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.speakers` |
| tables | `ev_speakers`, `ev_session_speakers` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../events/_module\|events.events]] | Speakers assigned to sessions |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | Speaker photos |

## Core Features

- **Speaker record** — name, bio, photo, title, company, social links; reusable across events.
- **Session assignment** — assign speakers to event sessions with a confirmation status (invited/confirmed/declined).
- **Logistics** — travel, accommodation, AV requirements, internal notes.
- **Public profile** on the event landing (confirmed speakers only).
- **Submission link** — a signed token lets a speaker submit/update their bio + photo.

## See features/

- [[features/speaker-directory|Speaker Directory]] — the reusable speaker records.
- [[features/session-assignment|Session Assignment]] — attach speakers to sessions with confirmation status.
- [[features/speaker-submit|Speaker Self-Submit]] — the public token bio/photo submission.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Landing shows confirmed speakers only; logistics never public.
- [ ] Duplicate session assignment rejected.
- [ ] Token submit updates bio/photo; invalid token → 404.
- [ ] Bio purified.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | event sessions | events.events | Assignment targets `ev_sessions` |

**Data ownership:** `events.speakers` writes only `ev_speakers` + `ev_session_speakers`. Sessions are read from the Events service; speakers never write `ev_sessions`. No cross-domain events ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../events/_module|Events]] · [[../sponsors/_module|Sponsors]]
- [[../_index|Events MOC]]
