---
domain: events
module: speakers
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers — Architecture

## Assignment Status

Per `(session, speaker)`: `invited → confirmed | declined`. Public landing renders `confirmed` speakers only.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `AssignSpeakerAction::run(sessionId, speakerId)` | lorisleiva action | Create an `ev_session_speakers` row (invited); send an invite notification *(assumed)*. Rejects duplicate `(session, speaker)`. |
| `ConfirmSpeakerAction` | lorisleiva action | Flip to `confirmed` (via mail link or admin). |
| `SpeakerSubmitController` | controller | Public signed-token endpoint to update bio + photo. |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `SpeakerResource` | Speakers | #1 CRUD resource | Directory; copy submit-link action. |
| Session assignment | relation on `EventResource` sessions | relation manager | Confirmation badges. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.speakers.view-any')
        && BillingService::hasModule('events.speakers');
}
```

Public submit + public profiles (confirmed only) use a guest/token guard (Vue + Inertia).

## Events

None fired or consumed. See [[../../../architecture/event-bus]].

## Uploads

- Speaker photo via Media Library; MIME whitelist + size cap enforced on both admin and public-token writes. See [[security]].
