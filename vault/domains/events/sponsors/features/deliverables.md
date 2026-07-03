---
domain: events
module: sponsors
feature: deliverables
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Deliverables Tracking

A per-sponsor checklist of contractual deliverables with due dates and overdue reminders.

## Behaviour

- Each sponsor has deliverables (description, status open/done, optional due date).
- `DeliverableReminderCommand` sends one overdue reminder per deliverable (guarded by `reminded`).
- Marking done completes the checklist item.

## UI

- **Kind**: simple-resource (relation manager)
- **Page**: deliverables relation manager on `SponsorResource`.
- **Layout**: checklist table (description, due date, status, reminded); inline add/edit; overdue rows highlighted.
- **Key interactions**: add deliverable → set due date; toggle done; overdue badge.
- **States**: empty (no deliverables → CTA) · loading (skeleton) · error (validation) · selected (edit) · overdue (red highlight).
- **Gating**: `events.sponsors.manage`.

## Data

- Owns / writes: `ev_sponsor_deliverables` only.
- Reads: parent sponsor (own).
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing cross-domain (reminder mails via foundation.email).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Deliverable due-date logic; `reminded` flag blocks duplicate reminders

### Feature (Pest)
- [ ] `DeliverableReminderCommand` reminds each overdue deliverable once; completed items skipped
- [ ] Tenant isolation: checklist per company

### Livewire
- [ ] Deliverables relation manager adds/completes items; overdue badge renders

## Unknowns

- Reminder re-fire on due-date change — see [[../unknowns]].

## Related

- [[../_module|Sponsors]] · [[sponsor-management]]
