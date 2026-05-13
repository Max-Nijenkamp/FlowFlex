---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.sequences
status: planned
color: "#4ADE80"
---

# Sales Sequences

> Automated outreach sequences — email cadences, call reminders, LinkedIn task prompts, and manual step tasks triggered by contact enrolment.

**Panel:** `crm`
**Module key:** `crm.sequences`

## What It Does

Sales Sequences automates multi-step outreach campaigns for prospecting and follow-up. A sequence is a series of steps — send email template, wait 3 days, log call reminder, wait 2 days, send follow-up email, etc. Contacts (or segments) are enrolled into a sequence and progress through steps automatically. Email steps send via the connected email account (Email Integration module). Task steps create activity reminders for the rep. When a contact replies to an email, they are automatically unenrolled from the sequence. Sequence analytics show open rates, reply rates, and meeting booked rates.

## Features

### Core
- Sequence definition: name, steps (ordered), and enrolment settings
- Step types: email (using a template), wait (N days), task (create an activity reminder for the rep), LinkedIn (manual prompt)
- Email step: uses Email Integration to send — tracked for opens and replies; auto-unenrol on reply
- Manual enrolment: rep enrolls one or more contacts into a sequence from the contact list or a segment
- Enrolment status per contact: active / paused / completed / replied / unenrolled

### Advanced
- Auto-unenrol triggers: configurable — reply to any email in the sequence, meeting booked, contact status changes to customer
- Branching: if-then branches based on email open or reply — e.g. send a different follow-up if the first email was opened vs not opened
- A/B testing: two variants of an email step — system splits enrolments 50/50 and reports open/reply rates per variant
- Bulk enrolment: enrol an entire Customer Segment into a sequence with one action
- Unsubscribe management: contacts who click "unsubscribe" in any sequence email are permanently excluded from all future sequence emails — GDPR compliant

### AI-Powered
- Send time optimisation: AI analyses recipient timezone and historical email open patterns to send each step at the time when that specific contact is most likely to open it
- Content personalisation: AI injects a personalised first line into each email step based on the contact's recent company news or LinkedIn activity — increases reply rates

## Data Model

```erDiagram
    crm_sequences {
        ulid id PK
        ulid company_id FK
        string name
        string status
        integer total_steps
        timestamps created_at/updated_at
    }

    crm_sequence_steps {
        ulid id PK
        ulid sequence_id FK
        integer step_order
        string type
        integer wait_days
        ulid email_template_id FK
        text task_description
        timestamps created_at/updated_at
    }

    crm_sequence_enrolments {
        ulid id PK
        ulid sequence_id FK
        ulid contact_id FK
        ulid company_id FK
        integer current_step
        string status
        timestamp enrolled_at
        timestamp unenrolled_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | email / wait / task / linkedin |
| `status` | active / paused / completed / replied / unenrolled |
| `current_step` | Which step number the contact is currently on |

## Permissions

- `crm.sequences.view`
- `crm.sequences.create`
- `crm.sequences.enrol-contacts`
- `crm.sequences.view-analytics`
- `crm.sequences.manage-unsubscribes`

## Filament

- **Resource:** `SequenceResource`
- **Pages:** `ListSequences`, `CreateSequence`, `ViewSequence` (with step builder and enrolment analytics)
- **Custom pages:** None
- **Widgets:** `ActiveSequencesWidget` — count of active enrolments on CRM dashboard
- **Nav group:** Activities (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Outreach | Sales engagement and sequences |
| SalesLoft | Sales cadence automation |
| HubSpot Sequences | CRM-native email sequences |
| Apollo.io | Sales engagement sequences |

## Related

- [[contacts]]
- [[email-integration]]
- [[customer-segments]]
- [[activities]]
- [[revenue-intelligence]]
