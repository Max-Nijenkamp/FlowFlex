---
domain: marketing
module: email-sequences
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Email Sequences

Automated multi-step email drip sequences triggered by events (form submission, segment entry, contact created). Nurture leads automatically. Bulk marketing automation — distinct from 1:1 rep-driven [[../../crm/sales-sequences/_module|sales-sequences]].

## Module-key

`marketing.sequences`

**Priority:** p3  
**Panel:** marketing  
**Permission prefix:** `marketing.sequences`  
**Tables:** `mkt_sequences`, `mkt_sequence_steps`, `mkt_sequence_enrolments`

## Core Features

- Sequence = ordered steps (email + wait days) + an enrolment trigger.
- Triggers: form submitted, added to segment, contact created, manual *(date-based deferred)*.
- Linear v1 (branch-by-open/click deferred *(assumed)*).
- Enrolment: one active per `(contact, sequence)`; contacts progress automatically on a cursor.
- Exit conditions: unsubscribe (suppression list), becomes customer *(assumed)*, manual unenrol.
- Pause/resume a sequence (pauses all its enrolments). Suppression list always honoured (shared `mkt_unsubscribes`, owned by [[../campaigns/_module|campaigns]]).

See [[features/build-sequence]] · [[features/enrolment-triggers]] · [[features/advancement-engine]].

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | enrolments target contacts |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | advancement, sending |
| Hard | [[../../foundation/email-setup/_module\|foundation.email]] | step mail transport |
| Soft | [[../forms/_module\|marketing.forms]] | form-submit trigger (consumes `FormSubmissionReceived`) |
| Soft | [[../../crm/customer-segments/_module\|crm.segments]] | segment-entry trigger (nightly diff *(assumed)*) |

## Sibling notes

- [[architecture]] — service, listeners, advancement command
- [[data-model]] — three tables + ERD
- [[api]] — `CreateSequenceData` DTO, consumed event
- [[security]] — permissions, suppression, tenant scoping
- [[decisions]] · [[unknowns]]
- [[features/build-sequence]] · [[features/enrolment-triggers]] · [[features/advancement-engine]]

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `FormSubmissionReceived` | [[../forms/_module\|marketing.forms]] | enrol per `trigger_config` |
| Reads | `SegmentService` membership | [[../../crm/customer-segments/_module\|crm.segments]] | segment-entry diff (read-only) |
| Reads | contact record | [[../../crm/contacts/_module\|crm.contacts]] | enrolment target (read-only) |

**Data ownership:** writes **only** `mkt_sequences`, `mkt_sequence_steps`, `mkt_sequence_enrolments`. Reads CRM contacts/segments via their services; reacts to `FormSubmissionReceived` and writes only its own enrolment rows. Never writes CRM or forms tables ([[../../../security/data-ownership]]).

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

## Test Checklist

- [ ] Tenant isolation: the advancement sweep and enrolment only touch the acting company's rows; a company B `FormSubmissionReceived` never enrols into a company A sequence
- [ ] Module gating: `SequenceResource` + `SequenceEnrolmentResource` hidden when `marketing.sequences` inactive
- [ ] One active enrolment per `(sequence, contact)` — duplicate enrol is skipped
- [ ] Advancement sends the due step, sets `next_step_at = now + wait_days`, and completes at the last step (send-once under the cursor)
- [ ] Suppression re-checked at enrol and before each send; a suppressed contact is never enrolled or advanced
- [ ] `FormSubmissionReceived` enrols only when `trigger_config.form_id` matches
- [ ] Pause/resume flips `is_active` and halts/continues all enrolments

## Related

- [[../campaigns/_module|Campaigns]] · [[../forms/_module|Forms]]
- [[../../crm/sales-sequences/_module|Sales Sequences]] (1:1 rep cadences, separate) · [[../../../architecture/queue-jobs]]
