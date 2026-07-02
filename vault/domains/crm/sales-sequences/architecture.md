---
domain: crm
module: sales-sequences
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sales Sequences — Architecture

## State Machine

Enrolment `status` moves through `active` → `paused` / `completed` / `unenrolled`. `active` enrolments advance on schedule; `paused` halt until resumed; `completed` reached at the last step; `unenrolled` on manual removal or lifecycle-stage churn *(assumed)*.

## Services & Actions

Interface → Service: `SequenceServiceInterface` → `SequenceService`.

| Method | Purpose |
|---|---|
| `enrol(EnrolData): EnrolmentData` | Enrol a contact (and optional deal); rejects a second active enrolment in the same sequence. |
| `advanceDue(): AdvanceResult` | Scheduled advance. Per-enrolment `try/catch`: execute the step (queue email / create task activity / wait), set `next_step_at`, complete at last step. |
| `pause(id)` / `resume(id)` / `unenrol(id)` | Enrolment lifecycle controls. |
| `pauseOnReply(contactId): void` | Called by crm.email inbound sync when a reply is detected. |
| `performance(sequenceId): SequenceStatsData` | Reply rate, meetings booked, per-step and per-variant stats. |

## Events

Fires: none.

### Consumes

Payload contracts live in [[../../../architecture/event-bus]].

| Event | Listener | Behaviour |
|---|---|---|
| `DealWon` | `EnrollInSuccessSequenceListener` | Enrols account contacts into any sequence with trigger `deal-won`. Payload carries `company_id`, `deal_id`, `account_id` scalars. |
| `InvoicePaid` | `TriggerUpsellSequenceListener` | Enrols per `invoice-paid` trigger rules. Payload carries `company_id`, `invoice_id` scalars. |

Both listeners `implements ShouldQueue` + `WithCompanyContext`. No matching sequence = no-op.

## Filament Artifacts

| Artifact | Nav group | Kind (ui-strategy) | Notes |
|---|---|---|---|
| `SequenceResource` | Activities | Standard CRUD resource | Step repeater builder, performance tab |
| `SequenceEnrolmentResource` | Activities | Standard CRUD resource | Who's where; pause/unenrol actions |
| Enrol action | Activities | Table/view action | On Contact + Deal |

Access contract:

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.sequences.view-any')
        && hasModule('crm.sequences');
}
```

Rich-text sanitize (medium): HTMLPurifier runs on sequence email-step template HTML on save — consistent with crm.email body purification.

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

## Jobs & Scheduling

| Job | Queue | Schedule | Purpose |
|---|---|---|---|
| `AdvanceSequencesCommand` | crm-queue → default | Every 15 min | Guard `next_step_at <= now AND status=active`; step execution advances the cursor in the same transaction. |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None.
