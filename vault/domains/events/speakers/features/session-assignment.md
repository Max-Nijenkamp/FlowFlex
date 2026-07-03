---
domain: events
module: speakers
feature: session-assignment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Session Assignment

Attach speakers to event sessions with an invited/confirmed/declined status.

## Behaviour

- `AssignSpeakerAction` creates an `ev_session_speakers` row (invited); duplicate `(session, speaker)` rejected.
- `ConfirmSpeakerAction` flips to confirmed (admin or mail link).
- Only confirmed assignments render on the public landing.

## UI

- **Kind**: simple-resource (relation manager)
- **Page**: session-speakers relation manager on the `EventResource` sessions (assignment picker with confirmation badges).
- **Layout**: per-session list of assigned speakers with status badge; add-speaker picker (from the directory).
- **Key interactions**: assign speaker → invited; confirm/decline toggle → badge updates.
- **States**: empty (no speakers assigned) · loading (skeleton) · error (duplicate assignment) · selected (assignment row).
- **Gating**: `events.speakers.assign`.

## Data

- Owns / writes: `ev_session_speakers` only.
- Reads: sessions (Events service), speaker directory (own).
- Cross-domain writes: NONE — sessions are read, never written ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: confirmed assignments render on the public landing + agenda.
- Shared entity: `ev_sessions` (owned by [[../../events/_module|Events]], read-only here).

## Test Checklist

### Unit
- [ ] Duplicate `(session, speaker)` rejected; status set invited on assign

### Feature (Pest)
- [ ] Confirm via mail link or admin flips invited->confirmed once under race; declined recorded
- [ ] Tenant isolation: assignments within own-company events only

### Livewire
- [ ] Assign action validates duplicates; status badges render; gated by the speakers permission

## Unknowns

- Decline notification / slot-freeing behaviour — see [[../unknowns]].

## Related

- [[../_module|Speakers]] · [[speaker-directory]] · [[../../events/features/agenda-sessions|Agenda & Sessions]]
