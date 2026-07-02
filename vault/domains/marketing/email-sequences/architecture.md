---
domain: marketing
module: email-sequences
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `SequenceResource` | Sequences | #1 CRUD resource | step repeater, trigger config, per-step stats |
| `SequenceEnrolmentResource` | Sequences | #1 CRUD resource | who/where, unenrol action |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.sequences.view-any')
        && BillingService::hasModule('marketing.sequences');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `AdvanceMarketingSequencesCommand` | notifications | every 15 min | `next_step_at <= now AND active` guard; cursor advanced in transaction |
| `SegmentEntryDiffCommand` | default | nightly | enrolment uniqueness guard |

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/queue-jobs]] · [[../../../architecture/event-bus]]
