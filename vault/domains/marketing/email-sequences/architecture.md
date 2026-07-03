---
domain: marketing
module: email-sequences
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Email Sequences — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `MarketingSequenceService::enrol` | `enrol(sequenceId, contactId)` | Duplicate-active guard + suppression check; creates enrolment `status=active, current_step=0, next_step_at=now`. |
| `MarketingSequenceService::advanceDue` | `advanceDue(): AdvanceResult` | Sends the due step's mail, schedules next `next_step_at = now + wait_days`, or completes at last step; per-enrolment try/catch. |
| `MarketingSequenceService::exit` | `exit(enrolmentId, reason)` | `status=exited`; used by unsubscribe / manual / lifecycle. |
| `EnrolFromFormListener` | queued | On `FormSubmissionReceived`; enrols when `trigger_config` matches the form. |
| `AdvanceMarketingSequencesCommand` | scheduled (15 min) | Cursor sweep: `next_step_at <= now AND status=active`, advanced in a transaction. |
| `SegmentEntryDiffCommand` | scheduled (nightly) | Diffs segment membership; enrols new members once (uniqueness guard). |

## Enrolment states

`active → paused` (sequence paused) · `active → completed` (last step sent) · `active → exited` (unsubscribe / customer / manual). Simple enum (no spatie states *(assumed)*).

## Events

Consumes `FormSubmissionReceived` (from [[../forms/_module|forms]]). Fires none. See [[../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Sequences

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SequenceResource` | #1 CRUD resource | tweaks: inline-relation-repeater (steps), state-badge-column (active/paused), custom-header-actions (pause / resume) | trigger config, per-step stats on view page |
| `SequenceEnrolmentResource` | #1 CRUD resource | tweaks: read-only-flow-owned (rows created by triggers/listener), custom-header-actions (unenrol) | who/where, `current_step`, `next_step_at`, status; manual enrol / unenrol names the `panel-action` limiter ([[./security]]) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('marketing.sequences.view-any') && BillingService::hasModule('marketing.sequences')`
per [[../../../architecture/filament-patterns]] #1. This module has no custom Filament pages and no public surface of its own — unsubscribe is the shared campaigns token endpoint ([[../campaigns/security]]). System sends run on the `notifications` queue under `CompanyContext`, not through a panel artifact.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Sequence / step CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Enrolment create (manual enrol, `EnrolFromFormListener`, segment diff) | Pessimistic | `DB::transaction()` + `lockForUpdate()` (or a `(sequence_id, contact_id)` active-uniqueness guard) so a contact is never double-enrolled in one active run |
| Step advancement (`advanceDue` cursor + send) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the enrolment row; send-once then reschedule `next_step_at` — the cursor guarantees a step never double-sends within a sweep |
| Pause / resume / exit / unenrol (status flip) | Optimistic | Simple-enum flip; `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) — not a spatie state machine |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `AdvanceMarketingSequencesCommand` | notifications | every 15 min | `next_step_at <= now AND active` guard; cursor advanced in transaction |
| `SegmentEntryDiffCommand` | default | nightly | enrolment uniqueness guard |

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/queue-jobs]] · [[../../../architecture/event-bus]]
