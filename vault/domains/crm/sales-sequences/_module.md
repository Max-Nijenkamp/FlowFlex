---
domain: crm
module: sales-sequences
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Sales Sequences

Automated outreach sequences — multi-step email/call cadences for sales reps to nurture leads and follow up with deals.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

`crm.sequences`

**Priority:** v1  
**Panel:** crm  
**Permission prefix:** `crm.sequences`  
**Tables:** `crm_sequences`, `crm_sequence_steps`, `crm_sequence_enrolments`

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|Contacts]] | Enrolments target contacts |
| Hard | [[../../crm/activities/_module\|Activities]] | Task steps land on the contact timeline |
| Hard | [[../../core/billing/_module\|Billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions |
| Hard | [[../../foundation/queues/_module\|Queues]] | Step advancement runs on the queue |
| Soft | [[../../crm/email/_module\|Email]] | Email steps via connected mailbox + reply-pause; without it, email steps send via system mailer with no auto-pause *(assumed)* |
| Soft | [[../../crm/deals/_module\|Deals]] | Deal stage-change triggers |
| Soft | [[../../crm/customer-segments/_module\|Customer Segments]] | Segment-entry triggers |

## Core Features

- Sequence = ordered steps (email, call task, wait, LinkedIn task).
- Enrolment — enrol a contact or deal.
- Triggers: manual, deal stage change, segment entry, `DealWon` success sequence, `InvoicePaid` upsell sequence (per event-bus contracts).
- Steps: email with template, wait days, manual task (call/LinkedIn).
- Auto-pause on reply (needs crm.email).
- Per-step tracking (email open/click/reply).
- A/B test step variants — v1: two variants per email step, random split *(assumed)*.
- Sequence performance (meetings booked, reply rate).
- Personal vs team sequences.
- One active enrolment per (contact, sequence).
- Unenrol on lifecycle stage churned *(assumed)*.

## See features/

- [[features/enrolment-triggers|Enrolment triggers]]
- [[features/step-advancement|Step advancement]]
- [[features/ab-testing|A/B testing]]

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see/advance company B enrolments
- [ ] Module gating: artifacts hidden when `crm.sequences` inactive
- [ ] Double enrolment rejected; re-enrol after completion allowed.
- [ ] Advance executes email/task/wait steps in order with wait_days gaps.
- [ ] Advance idempotent (run twice in window = one step).
- [ ] Reply pauses enrolment (with crm.email).
- [ ] DealWon/InvoicePaid listeners enrol per trigger config; no matching sequence = no-op.
- [ ] A/B variants split and tracked per variant.
- [ ] Completion at last step.
- [ ] Performance stats over fixtures.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | contact read API | [[../contacts/_module\|crm.contacts]] | enrolments target contacts |
| Reads | segment audience API | [[../customer-segments/_module\|crm.segments]] | `SegmentService::contacts()` for segment-entry enrol |
| Consumes | `DealWon` / `DealLost` | crm.deals / finance | auto success/exit enrolment |
| Consumes | `InvoicePaid` | finance | upsell-cadence enrolment |
| Consumes | `EmailReplied` | [[../email-integration/_module\|crm.email]] | auto-pause enrolment on reply |
| Consumes | `AppointmentBooked` *(assumed)* | scheduling | auto-exit enrolment (goal met) |
| Fires | step send (email via crm.email send API / task via activities) | [[../email-integration/_module\|crm.email]], [[../activities/_module\|crm.activities]] | sequence steps executed downstream |

**Data ownership:** `crm.sequences` writes only `crm_sequences`, `crm_sequence_steps`, `crm_sequence_enrolments`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../../crm/deals/_module|Deals]]
- [[../../crm/email/_module|Email Integration]]
- [[../../marketing/email-sequences/_module|Marketing Email Sequences]] — bulk marketing automation; a different module (this module is 1:1 rep-driven)
- [[../../../architecture/event-bus]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
