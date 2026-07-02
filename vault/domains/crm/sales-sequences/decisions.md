---
domain: crm
module: sales-sequences
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sales Sequences — Decisions

## ADR: One active enrolment per (contact, sequence)

A contact can only be actively enrolled once in a given sequence, enforced by a unique active `(sequence_id, contact_id)` constraint. Re-enrolment is allowed after the prior enrolment completes. This prevents duplicate cadences to the same person.

## ADR: A/B split — two variants, random

Email steps support A/B testing with two variants per step, assigned by random split at enrolment and stored in `variant_map`. Broader multivariate testing is out of scope for v1. *(assumed)*

## ADR: Personal vs team sequences

`owner_id` distinguishes personal sequences (owned by a rep) from team sequences (null owner). `crm.sequences.manage-team` gates team-sequence management.

## ADR: Degraded behaviour without crm.email

crm.email is a soft dependency. Without it, email steps send via the system mailer and there is no auto-pause on reply. `pauseOnReply()` is only invoked by crm.email's inbound sync. *(assumed)*

## ADR: Idempotent advancement

`AdvanceSequencesCommand` guards on `next_step_at <= now AND status=active` and advances the cursor in the same transaction as step execution, so a re-run within the same window does not double-execute a step.

## Related

- [[../../../architecture/event-bus]]
- [[unknowns]]
