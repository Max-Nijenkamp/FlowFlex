---
type: module
domain: Customer Success
panel: cs
phase: 5
status: planned
cssclasses: domain-cs
migration_range: 970100–970299
last_updated: 2026-05-09
---

# CS Playbooks & Alerts

Triggered playbook task lists when health drops, renewals approach, or milestones hit. Standardises CSM actions — ensures no customer falls through the cracks.

---

## Playbook Concept

A playbook is a reusable template of actions to run when a trigger fires:

```
Trigger: Health score drops to Red
Playbook: "At-Risk Recovery"
  Step 1 — [CSM] Call customer within 24 hours (due: today+1)
  Step 2 — [CSM] Identify root cause and document in notes (due: today+3)
  Step 3 — [CSM] Create internal escalation ticket if support issue (due: today+3)
  Step 4 — [CSM] Schedule executive sponsor call if churn risk (due: today+7)
  Step 5 — [CSM] Update renewal probability in system (due: today+7)
```

Steps can be: task (assignee + due date), email (auto-send or draft), meeting (calendar invite), notification (to CSM, manager, or customer).

---

## Trigger Types

| Trigger | Condition |
|---|---|
| Health score change | Drops to Red / Drops ≥ 10 points |
| Renewal date | N days before renewal (60, 30, 14, 7) |
| Onboarding milestone missed | Milestone past due date |
| No activity | CSM has logged no activity in X days |
| Usage drop | Product usage drops > 30% week-over-week |
| NPS response | Score ≤ 6 (detractor) received |
| Plan downgrade | Customer downgrades subscription |
| Support escalation | CSAT score < 3 on closed ticket |

---

## Alerts Inbox

CSM-facing inbox of all active playbook tasks:
- Today's tasks (overdue in red)
- This week's tasks
- Grouped by customer
- Quick-complete: mark done, add note, snooze

Manager view: team's overdue tasks, completion rate per CSM, playbook performance.

---

## Data Model

### `cs_playbooks`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | "At-Risk Recovery" |
| trigger_type | varchar(100) | |
| trigger_conditions | json | |
| steps | json | ordered array of step configs |
| active | bool | |

### `cs_playbook_runs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| playbook_id | ulid | FK |
| crm_company_id | ulid | FK |
| csm_id | ulid | FK `employees` |
| triggered_at | timestamp | |
| status | enum | active/completed/cancelled |
| completed_at | timestamp | nullable |

### `cs_playbook_tasks`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| run_id | ulid | FK |
| step_order | int | |
| type | enum | task/email/meeting/notification |
| title | varchar(300) | |
| assignee_id | ulid | FK `employees` |
| due_at | datetime | |
| completed_at | timestamp | nullable |
| notes | text | nullable |

---

## Migration

```
970100_create_cs_playbooks_table
970101_create_cs_playbook_runs_table
970102_create_cs_playbook_tasks_table
```

---

## Related

- [[MOC_CustomerSuccess]]
- [[customer-health-scoring]] — trigger source
- [[renewal-forecasting]] — renewal playbooks
- [[customer-onboarding-tracking]] — onboarding playbooks
