---
domain: crm
module: sales-sequences
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Activities

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SequenceResource` | #1 CRUD resource | tweaks: inline-relation-repeater (step builder), view-page-tabs (performance tab), custom-header-actions (activate / A/B config) | ordered step builder; per-variant performance tab |
| `SequenceEnrolmentResource` | #1 CRUD resource | tweaks: state-badge-column (`active`/`paused`/`completed`/`unenrolled`), custom-header-actions (pause / resume / unenrol) | who's where; enrolment lifecycle controls |
| Enrol action (Contact / Deal) | #1 CRUD resource | tweaks: custom-header-actions (enrol in sequence) | manual `crm.sequences.enrol` action on Contact + Deal views |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.sequences.view-any') && BillingService::hasModule('crm.sequences')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not
auto-gate them (this module has none; all surfaces are standard resources). Rich-text sanitize (medium):
HTMLPurifier runs on sequence email-step template HTML on save, consistent with crm.email body purification.

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Sequence / step CRUD (builder form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Enrol (one active per contact, sequence) | Pessimistic | `unique(contact_id, sequence_id) WHERE status=active` + `DB::transaction()` so a concurrent double-enrol yields one row |
| Step advancement (`advanceDue`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the enrolment; cursor move committed with step execution → idempotent per window |
| Enrolment transition (pause / resume / unenrol / complete) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read status, write per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job | Queue | Schedule | Purpose |
|---|---|---|---|
| `AdvanceSequencesCommand` | crm-queue → default | Every 15 min | Guard `next_step_at <= now AND status=active`; step execution advances the cursor in the same transaction. |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None.
