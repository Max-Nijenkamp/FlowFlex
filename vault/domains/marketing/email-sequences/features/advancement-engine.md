---
domain: marketing
module: email-sequences
feature: advancement-engine
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Advancement Engine

Move enrolments through their steps on time, idempotently, honouring suppression.

## Behaviour

- `AdvanceMarketingSequencesCommand` (every 15 min) selects `next_step_at <= now AND status=active`.
- For each: send the current step's mail, set `next_step_at = now + wait_days`, `current_step++`; at the last step → `completed`.
- Re-check suppression before each send; unsubscribed → `exit`.
- Cursor moves in a transaction → idempotent within the sweep window.

## UI

- **Kind**: background
- **Trigger**: scheduled command on the `notifications` queue. No page; progress visible per-enrolment in `SequenceEnrolmentResource` (`current_step`, `next_step_at`, status).
- **States**: n/a (background). Errors: per-enrolment try/catch isolates a bad send; failures logged, not fatal to the sweep.
- **Gating**: runs under `CompanyContext`; no user gate (system job).

## Data

- Owns / writes: `mkt_sequence_enrolments` (cursor + status) (own module).
- Reads: steps (own), `mkt_unsubscribes` (suppression) — read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: per-step engagement consumed by [[../../marketing-analytics/_module|Marketing Analytics]].
- Shared entity: `mkt_unsubscribes` (owned by campaigns).

## Unknowns

- Reuse campaign open/click tracking exactly vs. a lighter counter. See [[../unknowns]].

## Related

- [[../_module|Email Sequences]] · [[enrolment-triggers]] · [[../architecture]]
