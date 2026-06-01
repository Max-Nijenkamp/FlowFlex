---
type: module
domain: Customer Success
panel: crm
module-key: cs.playbooks
status: planned
color: "#4ADE80"
---

# CS Playbooks

Repeatable success playbooks: sequences of tasks triggered by customer lifecycle events (onboarding, renewal, at-risk, expansion).

## Core Features

- Playbook: name, trigger, ordered tasks/steps
- Triggers: new customer, renewal approaching, health drop, milestone reached, manual
- Steps: task with description, owner role, due offset (days from trigger)
- Playbook run: instance per account, tracks step completion
- Step completion tracking
- Templates: onboarding playbook, renewal playbook, at-risk recovery playbook
- Auto-assign steps to CSM (Customer Success Manager)

## Data Model

| Table | Key Columns |
|---|---|
| `cs_playbooks` | company_id, name, trigger_type, trigger_config (json) |
| `cs_playbook_steps` | playbook_id, company_id, title, description, owner_role, day_offset, order |
| `cs_playbook_runs` | playbook_id, company_id, account_id, status, started_at, completed_at |
| `cs_playbook_run_steps` | run_id, step_id, company_id, status, completed_at, assignee_id |

## Filament

**Nav group:** Playbooks

- `PlaybookResource` — build playbooks (step repeater)
- `PlaybookRunResource` — active runs, step progress

## Cross-Domain / Events

- Triggered by health score changes, renewal dates, new customer events

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/qbr]]
