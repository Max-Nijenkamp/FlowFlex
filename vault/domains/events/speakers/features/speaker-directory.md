---
domain: events
module: speakers
feature: speaker-directory
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Speaker Directory

The reusable, company-level speaker records shared across events.

## Behaviour

- CRUD speaker profiles: name, bio (sanitized), photo, title, company, social links, internal logistics.
- Reused across events via session assignments.
- "Copy submit link" action produces the signed self-submit URL.

## UI

- **Kind**: simple-resource
- **Page**: `SpeakerResource` list + form at `/app/events/speakers` (nav group "Speakers").
- **Layout**: table (photo, name, title, company, # assignments); form with bio editor + photo upload + social links repeater + logistics section.
- **Key interactions**: create/edit speaker; copy submit link; view assignments.
- **States**: empty (no speakers → CTA) · loading (skeleton) · error (validation) · selected (edit form).
- **Gating**: `events.speakers.view-any`; edit needs `events.speakers.manage`.

## Data

- Owns / writes: `ev_speakers` only.
- Reads: assignment counts (own `ev_session_speakers`).
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: profiles referenced by [[session-assignment|Session Assignment]] + public landing.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Speaker record validation (name, bio purified, photo upload contract)

### Feature (Pest)
- [ ] Directory reusable across events (company-level records); tenant isolation enforced

### Livewire
- [ ] Speaker resource CRUD; hidden without permission or `events.speakers` inactive

## Unknowns

- Logistics encryption — see [[../unknowns]].

## Related

- [[../_module|Speakers]] · [[session-assignment]] · [[speaker-submit]]
