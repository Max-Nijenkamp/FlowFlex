---
domain: crm
module: sales-sequences
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sales Sequences — Unknowns

## Assumptions

- *(assumed)* Without crm.email, email steps send via the system mailer and there is no auto-pause on reply.
- *(assumed)* A/B testing uses two variants per email step, assigned by random split.
- *(assumed)* Enrolments unenrol automatically when a contact's lifecycle stage becomes churned.

## Open Questions

- Should the churned-unenrol be driven by a `ContactLifecycleChanged` event, or polled during advancement?
- When crm.email is absent, should email steps be blocked at save time or silently degrade to system mailer?
- How should reply detection map to enrolments when a contact is in multiple sequences at once?
- Is a 15-minute advance cadence granular enough for time-sensitive follow-ups, or should it be configurable?
- Do team sequences need per-rep sending identity, or send from a shared team mailbox?
