---
domain: crm
module: sales-sequences
type: feature
feature: step-advancement
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Step Advancement

Enrolments move through their sequence's ordered steps on a scheduled cadence.

## The advance loop

`AdvanceSequencesCommand` runs every 15 minutes and calls `SequenceService::advanceDue()`. It selects due enrolments with the guard `next_step_at <= now AND status = active` (backed by the `(company_id, status, next_step_at)` index).

For each due enrolment, in a `try/catch`:

1. Execute the current step by `type`:
   - `email` → queue `SequenceStepMail` (variant-aware).
   - `call` / `task` → create a task activity on the contact timeline.
   - `wait` → no action beyond advancing the cursor.
2. Advance `current_step` and set `next_step_at` from the next step's `wait_days`.
3. If the last step is reached, set `status = completed`.

Cursor advancement happens in the same transaction as step execution.

## Idempotency

Because advancement is guarded on `next_step_at` and committed transactionally with the cursor move, running the command twice within one window executes each step only once.

## Pause on reply

When crm.email detects an inbound reply, it calls `SequenceService::pauseOnReply(contactId)`, moving the enrolment to `paused`. A rep can `resume` or `unenrol`. Without crm.email, this pause never fires *(assumed)*.

## UI
- **Kind**: background — a scheduled step-processor job; no interactive page. Rep actions (resume/unenrol) live on `SequenceEnrolmentResource`.
- **Page**: no dedicated page. Trigger: `AdvanceSequencesCommand` (every 15 min) → `SequenceService::advanceDue()`. Enrolment state shown on `SequenceEnrolmentResource`.
- **Layout**: n/a (headless). Enrolment list shows `current_step`, `status`, `next_step_at`.
- **Key interactions**: none direct for advance; rep can `resume` / `unenrol` a paused enrolment from the resource.
- **States**: empty (no due enrolments) · loading (command running) · error (per-enrolment try/catch; one failure doesn't abort the batch) · selected (paused enrolment awaiting resume/unenrol)
- **Gating**: `crm.sequences.manage` *(assumed)* for resume/unenrol; job runs under company context

## Data
- Owns / writes: `crm_sequence_enrolments` (advances `current_step` / `next_step_at`, sets `status`), reads `crm_sequence_steps` (step type, `wait_days`, variant)
- Reads: [[../../contacts/_module|crm.contacts]] for task/email targeting (read-only)
- Cross-domain writes: via events only ([[../../../../security/data-ownership]]) — email steps sent via crm.email send API; task steps created by dispatching to activities, not writing `crm_activities` directly

## Relations
- Consumes: scheduled tick → advance; `EmailReplied` from [[../../email-integration/_module|crm.email]] → `pauseOnReply`
- Feeds: `SequenceStepMail` queued (email step); task-step request → [[../../activities/_module|crm.activities]]; `SequenceCompleted` *(assumed)* at last step
- Shared entity: contacts

## Test Checklist

### Unit
- [ ] Next `next_step_at` computed from the next step's `wait_days`; the last step sets `status = completed`
- [ ] Step dispatch by type: `email` → mail, `call`/`task` → activity, `wait` → cursor-only

### Feature (Pest)
- [ ] `advanceDue()` executes due steps in order with `wait_days` gaps; running twice in one window advances once (idempotent, transactional cursor)
- [ ] `pauseOnReply(contactId)` moves the enrolment to `paused`; resume/unenrol restore control
- [ ] Per-enrolment try/catch isolates a failing enrolment; the batch continues; tenant context preserved on the queue

## Related

- [[../architecture]]
- [[enrolment-triggers]]
- [[ab-testing]]
