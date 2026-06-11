---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.sequences
status: complete
priority: v1
depends-on: [crm.contacts, crm.activities, core.billing, core.rbac, foundation.queues]
soft-depends: [crm.email, crm.deals, crm.segments]
fires-events: []
consumes-events: [DealWon, InvoicePaid]
patterns: [queues, events]
tables: [crm_sequences, crm_sequence_steps, crm_sequence_enrolments]
permission-prefix: crm.sequences
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Sales Sequences

Automated outreach sequences — multi-step email/call cadences for sales reps to nurture leads and follow up with deals.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] + [[domains/crm/activities\|crm.activities]] | enrolments target contacts; tasks land on the timeline |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, step advancement |
| Soft | [[domains/crm/email-integration\|crm.email]] | email steps via connected mailbox + reply-pause; without it email steps send via system mailer, no auto-pause *(assumed)* |
| Soft | [[domains/crm/deals\|crm.deals]], [[domains/crm/customer-segments\|crm.segments]] | stage/segment triggers |

---

## Core Features

- Sequence: ordered steps (email, call task, wait, LinkedIn task)
- Enrolment: enrol a contact or deal into a sequence
- Triggers: manual, deal stage change, segment entry, `DealWon` (success sequence), `InvoicePaid` (upsell sequence per event-bus contracts)
- Steps: email (with template), wait days, manual task (call/LinkedIn)
- Auto-pause on reply: stop sequence when prospect responds (needs crm.email)
- Per-step tracking: email open/click/reply rates
- A/B test step variants *(v1: two variants per email step, random split *(assumed)*)*
- Sequence performance: meetings booked, reply rate
- Personal vs team sequences
- One active enrolment per (contact, sequence); unenrol on lifecycle stage `churned` *(assumed)*

---

## Data Model

### crm_sequences

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| owner_id | ulid FK | personal; null = team |
| trigger_type | string | manual / stage-change / segment-entry / deal-won / invoice-paid |
| trigger_config | jsonb nullable | stage id / segment id |
| is_active | boolean | |
| deleted_at | timestamp nullable | |

### crm_sequence_steps

| Column | Type | Notes |
|---|---|---|
| id, sequence_id FK, company_id | ulid | |
| order | int | unique `(sequence_id, order)` |
| type | string | email / call / wait / task |
| config | jsonb | template id(s)/variants, task text |
| wait_days | int default 0 | |

### crm_sequence_enrolments

| Column | Type | Notes |
|---|---|---|
| id, sequence_id FK, company_id (indexed) | ulid | |
| contact_id | ulid FK | unique active `(sequence_id, contact_id)` |
| deal_id | ulid nullable FK | |
| current_step | int default 0 | |
| status | string default `active` | active / paused / completed / unenrolled |
| next_step_at | timestamp | advancement cursor |
| variant_map | jsonb | A/B assignments |
| enrolled_at | timestamp | |

**Indexes:** `(company_id, status, next_step_at)` (advance query)

---

## DTOs

### CreateSequenceData — name, owner_id?, trigger_type (in set), trigger_config (required for non-manual), steps[{type, order, config, wait_days}] min:1
### EnrolData — sequence_id, contact_id (not already actively enrolled — "Contact is already in this sequence."), deal_id?

## Services & Actions

Interface→Service: `SequenceServiceInterface` → `SequenceService`.

- `enrol(EnrolData $data): EnrolmentData`
- `advanceDue(): AdvanceResult` — scheduled; per-enrolment try/catch: execute step (queue email / create task activity / wait), set `next_step_at`, complete at last step
- `pause(string $enrolmentId)` / `resume` / `unenrol`
- `pauseOnReply(string $contactId): void` — called by crm.email inbound sync
- `performance(string $sequenceId): SequenceStatsData`

## Events

### Consumes (contracts in [[architecture/event-bus]]):
- `DealWon` → `EnrollInSuccessSequenceListener` — enrols account contacts in sequences with trigger `deal-won`
- `InvoicePaid` → `TriggerUpsellSequenceListener` — per `invoice-paid` trigger rules

---

## Filament

**Nav group:** Activities

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SequenceResource` | #1 CRUD resource | step repeater builder, performance tab |
| `SequenceEnrolmentResource` | #1 CRUD resource | who's where, pause/unenrol actions |
| Enrol action | table/view action | on Contact + Deal |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.sequences.view-any') && BillingService::hasModule('crm.sequences')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rich-text sanitize** (medium): Note HTMLPurifier sanitization on sequence email-step template HTML on save (consistent with crm.email body purification).

---

## Permissions

`crm.sequences.view-any` · `crm.sequences.create` · `crm.sequences.update` · `crm.sequences.enrol` · `crm.sequences.manage-team`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `AdvanceSequencesCommand` | crm-queue→`default` | every 15 min | `next_step_at <= now AND status=active` guard; step execution advances cursor in same transaction |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Double enrolment rejected; re-enrol after completion allowed
- [ ] Advance executes email/task/wait steps in order with wait_days gaps
- [ ] Advance idempotent (run twice in window = one step)
- [ ] Reply pauses enrolment (with crm.email)
- [ ] `DealWon`/`InvoicePaid` listeners enrol per trigger config; no matching sequence = no-op
- [ ] A/B variants split and tracked per variant
- [ ] Completion at last step; performance stats over fixtures

---

## Build Manifest

```
database/migrations/xxxx_create_crm_sequences_table.php
database/migrations/xxxx_create_crm_sequence_steps_table.php
database/migrations/xxxx_create_crm_sequence_enrolments_table.php
app/Models/CRM/{Sequence,SequenceStep,SequenceEnrolment}.php
app/Data/CRM/{CreateSequenceData,EnrolData,EnrolmentData,SequenceStatsData}.php
app/Contracts/CRM/SequenceServiceInterface.php
app/Services/CRM/SequenceService.php
app/Listeners/CRM/{EnrollInSuccessSequenceListener,TriggerUpsellSequenceListener}.php
app/Console/Commands/CRM/AdvanceSequencesCommand.php
app/Mail/CRM/SequenceStepMail.php
app/Filament/CRM/Resources/{SequenceResource,SequenceEnrolmentResource}.php
database/factories/CRM/{SequenceFactory,SequenceEnrolmentFactory}.php
tests/Feature/CRM/{SequenceAdvanceTest,SequenceTriggerTest,SequencePauseTest}.php
```

---

## Related

- [[domains/crm/deals]]
- [[domains/crm/email-integration]]
- [[domains/marketing/email-sequences]] — bulk marketing automation (different module: 1:1 rep-driven here)
- [[architecture/event-bus]]
