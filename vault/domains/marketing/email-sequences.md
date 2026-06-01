---
type: module
domain: Marketing
panel: marketing
module-key: marketing.sequences
status: planned
color: "#4ADE80"
---

# Email Sequences

Automated multi-step email drip sequences triggered by events (form submission, segment entry, date). Nurture leads automatically.

## Core Features

- Sequence: ordered steps (email + wait delay), enrolment trigger
- Triggers: form submitted, added to segment, contact created, date-based, manual
- Steps: email content + wait days before next step
- Branching: conditional next step based on open/click behaviour (advanced)
- Enrolment: contacts enter and progress through steps automatically
- Exit conditions: stop sequence when contact converts/replies/unsubscribes
- Per-step tracking: open/click rates
- Pause/resume a sequence

## Data Model

| Table | Key Columns |
|---|---|
| `mkt_sequences` | company_id, name, trigger_type, trigger_config (json), is_active |
| `mkt_sequence_steps` | sequence_id, company_id, order, email_subject, email_body, wait_days |
| `mkt_sequence_enrolments` | sequence_id, company_id, contact_id, current_step, status, enrolled_at, completed_at |

## Filament

**Nav group:** Sequences

- `SequenceResource` — build sequence (step repeater), set trigger
- `SequenceEnrolmentResource` — view who is enrolled and at which step

## Cross-Domain / Jobs

- Scheduled job advances enrolments through steps (see [[architecture/queue-jobs]])
- Triggered by form submissions, segment changes

## Related

- [[domains/marketing/campaigns]]
- [[domains/marketing/forms]]
- [[domains/crm/sales-sequences]]
