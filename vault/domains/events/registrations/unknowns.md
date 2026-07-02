---
domain: events
module: registrations
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Registrations — Unknowns

## Assumed Items

- Custom registration questions are defined per event as a jsonb schema (`events.custom_questions`) *(assumed)* — the definition's ownership (events vs. registrations) is unspecified.
- `contact_id` on the registration is set by the CRM listener path *(assumed)* — the write-back mechanism (CRM fires a follow-up event vs. registrations reads CRM) is not designed.
- Post-event no-show marking runs on a fixed delay after `end_at` *(assumed)* — the exact window is unspecified.

## Open Questions

- Does the CRM contact link (`contact_id`) get written back to `ev_registrations`, and if so by which side without violating data ownership? (Candidate: a `ContactLinked` event from CRM that registrations' own listener applies.)
- Should waitlist promotion require re-confirmation (email opt-in) or auto-register the promoted attendee?
- GDPR: retention/purge policy for attendee PII after an event completes (DSAR + auto-delete) — see [[../../../architecture/data-lifecycle]].
- Group / multi-attendee registration in a single submission — v1 or deferred?
