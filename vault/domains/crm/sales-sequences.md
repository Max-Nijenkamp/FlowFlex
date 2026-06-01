---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.sequences
status: planned
color: "#4ADE80"
---

# Sales Sequences

Automated outreach sequences — multi-step email/call cadences for sales reps to nurture leads and follow up with deals.

## Core Features

- Sequence: ordered steps (email, call task, wait, LinkedIn task)
- Enrolment: enrol a contact or deal into a sequence
- Triggers: manual, deal stage change, segment entry
- Steps: email (with template), wait days, manual task (call/LinkedIn)
- Auto-pause on reply: stop sequence when prospect responds
- Per-step tracking: email open/click/reply rates
- A/B test step variants
- Sequence performance: meetings booked, reply rate
- Personal vs team sequences

## Data Model

| Table | Key Columns |
|---|---|
| `crm_sequences` | company_id, name, owner_id, trigger_type, is_active |
| `crm_sequence_steps` | sequence_id, company_id, order, type (email/call/wait/task), config (json), wait_days |
| `crm_sequence_enrolments` | sequence_id, company_id, contact_id, deal_id, current_step, status, enrolled_at |

## Filament

**Nav group:** Activities

- `SequenceResource` — build sequence (step repeater)
- `SequenceEnrolmentResource` — who is enrolled, at which step
- Enrol action on Contact/Deal

## Cross-Domain / Jobs

- Scheduled job advances enrolments (see [[architecture/queue-jobs]])
- Differs from [[domains/marketing/email-sequences]]: sales sequences are 1:1 rep-driven with call tasks; marketing sequences are bulk automated

## Related

- [[domains/crm/deals]]
- [[domains/crm/email-integration]]
- [[domains/marketing/email-sequences]]
