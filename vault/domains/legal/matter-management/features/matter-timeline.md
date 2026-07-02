---
domain: legal
module: matter-management
feature: matter-timeline
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Matter Timeline

Key events and deadlines on a matter, with a 7-day deadline alert.

## Behaviour

- Event rows: title, event_date, is_deadline, notes.
- `MatterDeadlineAlertCommand` (daily) alerts deadline events 7d out, once per event (`alerted` guard) *(assumed)*.
- Timeline renders chronologically on the matter view.

## UI

- **Kind**: custom-page — timeline is a bespoke chronological view (relation tab rendered as a vertical timeline, not a plain table).
- **Page**: "Timeline" tab on the matter view (`/legal/matters/{id}`).
- **Layout**: vertical timeline; deadline events flagged with a countdown chip; inline "add event" at top.
- **Key interactions**: add event (mark as deadline); edit/delete; deadlines highlighted as they approach.
- **States**: empty ("No events yet") · loading (skeleton timeline) · error (validation) · selected (event expanded with notes).
- **Gating**: `legal.matters.update` (within confidentiality scope).

## Data

- Owns / writes: `legal_matter_events`.
- Reads: `users` for `created_by` (platform).
- Cross-domain writes: none — deadline alerts via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: deadline notifications via `core.notifications`.
- Shared entity: `users` (platform).

## Test Checklist

### Unit
- [ ] Deadline alert window computes 7 days before `event_date`
- [ ] `alerted` guard prevents a second alert for the same event
- [ ] Non-deadline events are never alerted

### Feature (Pest)
- [ ] `MatterDeadlineAlertCommand` alerts a deadline event once at 7d out via `core.notifications`
- [ ] Re-running the command same day does not re-alert
- [ ] Timeline events inherit the parent matter's confidentiality scope (hidden to non-listed users)

### Livewire
- [ ] Timeline relation-manager add form validates required title + event_date, gates on `legal.matters.update`
- [ ] Deadline events render a countdown chip as they approach

## Unknowns

- `*(assumed)*` 7d deadline window; closing matter with open deadlines behaviour undefined — [[../unknowns]].

## Related

- [[../_module|Matter Management]] · [[./matter-records]]
