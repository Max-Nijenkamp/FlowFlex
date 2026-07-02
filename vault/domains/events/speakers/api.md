---
domain: events
module: speakers
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers — API / DTOs

## `CreateSpeakerData`

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `bio` | text | nullable; HTMLPurifier-sanitized |
| `title` | string | nullable |
| `company_name` | string | nullable |
| `social_links` | array | nullable; url values |

## `SpeakerSubmitData` (public token)

| Field | Type | Rules |
|---|---|---|
| `bio` | text | nullable; sanitized |
| `photo` | file | nullable; image MIME whitelist + size cap |

Rate-limited; resolves the speaker by signed `submit_token`.

## Command API (internal)

- `AssignSpeakerAction::run(sessionId, speakerId)` — invited assignment.
- `ConfirmSpeakerAction` — flip to confirmed.

## Public / Portal Endpoints

| Route | Method | Auth | Purpose |
|---|---|---|---|
| speaker submit | GET/POST | signed token | Self-service bio/photo update. See [[features/speaker-submit]]. |
| public profile | GET | guest | Confirmed speaker on the event landing. |
