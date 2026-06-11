---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.sequences
status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, foundation.queues, foundation.email]
soft-depends: [marketing.forms, crm.segments]
fires-events: []
consumes-events: [FormSubmissionReceived]
patterns: [queues]
tables: [mkt_sequences, mkt_sequence_steps, mkt_sequence_enrolments]
permission-prefix: marketing.sequences
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Email Sequences

Automated multi-step email drip sequences triggered by events (form submission, segment entry, contact created). Nurture leads automatically. Bulk marketing automation — distinct from 1:1 rep-driven [[domains/crm/sales-sequences]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | enrolments target contacts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, advancement, sending |
| Soft | [[domains/marketing/forms\|marketing.forms]] | form-submit trigger (consumes event) |
| Soft | [[domains/crm/customer-segments\|crm.segments]] | segment-entry trigger (nightly diff *(assumed)*) |

---

## Core Features

- Sequence: ordered steps (email + wait delay), enrolment trigger
- Triggers: form submitted, added to segment, contact created, manual (date-based deferred *(assumed)*)
- Steps: email content + wait days before next step
- Branching by open/click deferred to later *(assumed: linear v1)*
- Enrolment: contacts enter and progress through steps automatically
- Exit conditions: unsubscribe (suppression list), lifecycle stage becomes customer *(assumed)*, manual unenrol
- Per-step tracking: open/click rates
- Pause/resume a sequence (pauses all enrolments)
- One active enrolment per (contact, sequence); suppression list always honored

---

## Data Model

### mkt_sequences — id, company_id (indexed), name, trigger_type (form/segment/contact-created/manual), trigger_config (jsonb), is_active, deleted_at
### mkt_sequence_steps — id, sequence_id FK, company_id, order (unique per sequence), email_subject, email_body (purified), wait_days
### mkt_sequence_enrolments

| Column | Type | Notes |
|---|---|---|
| id, sequence_id FK, company_id (indexed), contact_id FK | ulid | unique active `(sequence_id, contact_id)` |
| current_step | int default 0 | |
| status | string default `active` | active / paused / completed / exited |
| next_step_at | timestamp | advancement cursor |
| enrolled_at / completed_at | timestamp | |

**Indexes:** `(company_id, status, next_step_at)`

Step engagement: open/click tracked per send (campaign tracking machinery reused).

---

## DTOs

### CreateSequenceData — name, trigger_type (in set), trigger_config (validated per type), steps[{order, email_subject, email_body, wait_days}] min:1

## Services & Actions

- `MarketingSequenceService::enrol(string $sequenceId, string $contactId)` — duplicate-active + suppression checks
- `advanceDue(): AdvanceResult` — send step mail, schedule next; per-enrolment try/catch
- `exit(string $enrolmentId, string $reason)`
- Listeners: `EnrolFromFormListener` (FormSubmissionReceived per trigger_config), contact-created observer hook, segment-entry nightly diff job

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `AdvanceMarketingSequencesCommand` | notifications | every 15 min | `next_step_at <= now AND active` guard; cursor advanced in transaction |
| `SegmentEntryDiffCommand` | default | nightly | enrolment uniqueness guards |

---

## Filament

**Nav group:** Sequences

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SequenceResource` | #1 CRUD resource | step repeater, trigger config, per-step stats |
| `SequenceEnrolmentResource` | #1 CRUD resource | who/where, unenrol action |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('marketing.sequences.view-any') && BillingService::hasModule('marketing.sequences')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`marketing.sequences.view-any` · `marketing.sequences.create` · `marketing.sequences.update` · `marketing.sequences.enrol`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Form trigger enrols per config; duplicate active enrolment rejected
- [ ] Advance respects wait_days; idempotent within window
- [ ] Unsubscribed contact never enrolled/advanced
- [ ] Pause stops advancement; resume continues
- [ ] Completion at last step
- [ ] Segment-entry diff enrols new members once

---

## Build Manifest

```
database/migrations/xxxx_create_mkt_sequences_table.php
database/migrations/xxxx_create_mkt_sequence_steps_table.php
database/migrations/xxxx_create_mkt_sequence_enrolments_table.php
app/Models/Marketing/{MarketingSequence,SequenceStep,SequenceEnrolment}.php
app/Data/Marketing/CreateSequenceData.php
app/Services/Marketing/MarketingSequenceService.php
app/Listeners/Marketing/EnrolFromFormListener.php
app/Console/Commands/Marketing/{AdvanceMarketingSequencesCommand,SegmentEntryDiffCommand}.php
app/Mail/Marketing/SequenceStepMail.php
app/Filament/Marketing/Resources/{SequenceResource,SequenceEnrolmentResource}.php
database/factories/Marketing/{MarketingSequenceFactory,SequenceEnrolmentFactory}.php
tests/Feature/Marketing/{SequenceAdvanceTest,SequenceTriggerTest}.php
```

---

## Related

- [[domains/marketing/campaigns]]
- [[domains/marketing/forms]]
- [[domains/crm/sales-sequences]] — 1:1 rep cadences, separate module
