---
type: module
domain: Customer Success
panel: crm
module-key: cs.qbr
status: planned
color: "#4ADE80"
---

# QBR Management

Quarterly Business Review management: schedule, prepare, and track strategic reviews with key accounts.

## Core Features

- QBR record: account, scheduled date, status, attendees, agenda, outcomes
- QBR template: standard agenda + metrics to present
- Auto-generated QBR deck data: usage stats, health trend, support summary, ROI
- Action items from QBR with owners and due dates
- QBR cadence: schedule recurring QBRs per account tier
- Pre-QBR checklist for CSM
- QBR history per account
- Outcome notes and follow-up tracking

## Data Model

| Table | Key Columns |
|---|---|
| `cs_qbrs` | company_id, account_id, scheduled_at, status, attendees (json), agenda, outcomes, csm_id |
| `cs_qbr_action_items` | qbr_id, company_id, description, owner_id, due_date, status |

## Filament

**Nav group:** Accounts

- `QbrResource` — schedule, prepare, record outcomes
- Action items as relation manager
- QBR data auto-populated from health scores + usage

## Cross-Domain

- Pulls metrics from CRM, Support, health scores for the review deck

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/playbooks]]
