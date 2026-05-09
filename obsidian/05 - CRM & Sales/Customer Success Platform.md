---
tags: [flowflex, domain/crm, customer-success, health-score, churn, phase/3]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-08
---

# Customer Success Platform

Stop losing customers you didn't see coming. Health scores, success plans, playbooks, and QBR templates built into your CRM. CSMs work from one screen instead of juggling Gainsight, HubSpot, and a spreadsheet tracker.

**Who uses it:** Customer success managers, account managers, support leads
**Filament Panel:** `crm`
**Depends on:** Core, [[Contact & Company Management]], [[Customer Support & Helpdesk]], [[Shared Inbox & Email]], [[Revenue Intelligence & Forecasting]]
**Phase:** 3

---

## Features

### Customer Health Score

- Composite score 0–100 per customer account
- Configurable factors and weights (each factor scored 0–100, weighted average):
  - **Product usage** — logins, feature adoption, DAU/MAU ratio
  - **Support sentiment** — ticket volume, resolution time, CSAT scores
  - **Financial health** — payment history, outstanding invoices, MRR trend
  - **Engagement** — email open rates, QBR attendance, NPS score
  - **Relationship** — last CSM touch date, stakeholder breadth (how many contacts active)
- Risk bands: Healthy (70–100), At Risk (40–69), Critical (0–39)
- Trend arrow: improving / stable / declining vs 30 days ago
- Health score displayed on: contact list, account record, CS dashboard

### Success Plans

- Create a success plan per customer account
- Plan sections: goals, key milestones, risks, stakeholders, CSM notes
- Milestones: track completion with dates and status
- Shared view: generate a shareable link for the customer to view their own plan (read-only or collaborative edit)
- Templates: "SaaS Onboarding 90-day plan", "Enterprise Expansion plan", custom templates

### Playbooks

- Create automated playbooks triggered by health events:
  - Health drops below 50 → assign CSM task "schedule call"
  - NPS detractor submitted → escalate to CSM manager
  - No login in 14 days → auto-send re-engagement email
  - Contract renewal in 90 days → create renewal opportunity in Sales Pipeline
  - Usage milestone hit (100th export) → trigger upsell conversation task
- Playbook steps: task, email, notification, wait, condition branch
- Run history: which customers are in which playbook and at which step

### QBR Management (Quarterly Business Review)

- QBR template: customisable presentation outline
  - Executive summary
  - Health score trend
  - Support ticket summary
  - Product adoption stats
  - Goals achieved vs planned
  - Roadmap items relevant to this customer
  - Renewal discussion
- Auto-populate with live data from FlowFlex
- Present: slide-like format exportable to PDF
- Schedule: book QBR meeting linked to [[Booking & Appointment Scheduling]]

### Customer Segmentation for CS

- Segment by: tier (Enterprise/Mid-market/SMB), health band, ARR size, product
- CS capacity: assign customers to CSMs with workload limits (max 50 accounts per CSM)
- Auto-assign: new accounts auto-routed to CSM with capacity
- Coverage report: accounts without a CSM assigned

### Renewals Pipeline

- Renewal date pulled from subscription/contract record
- Renewal pipeline board: columns by stage (Identified → Engaged → Proposal → Closed)
- Auto-created: renewal opportunity created 90 days before contract end
- At-risk renewals flagged by health score
- Renewal forecast: ARR renewing this quarter by probability band

---

## Database Tables (3)

### `crm_health_scores`
| Column | Type | Notes |
|---|---|---|
| `contact_id` | ulid FK | → crm_companies |
| `score` | decimal | 0–100 |
| `band` | enum | `healthy`, `at_risk`, `critical` |
| `trend` | enum | `improving`, `stable`, `declining` |
| `factor_breakdown` | json | {factor: score} |
| `computed_at` | timestamp | |

### `crm_success_plans`
| Column | Type | Notes |
|---|---|---|
| `company_id` | ulid FK | |
| `csm_id` | ulid FK | |
| `template_id` | ulid FK nullable | |
| `goals` | json | [{title, status, target_date}] |
| `milestones` | json | [{title, status, due_date}] |
| `risks` | json | [{title, severity, mitigation}] |
| `share_token` | string nullable | public view link |
| `last_reviewed_at` | timestamp nullable | |

### `crm_playbook_runs`
| Column | Type | Notes |
|---|---|---|
| `playbook_id` | ulid FK | |
| `company_id` | ulid FK | |
| `current_step` | integer | |
| `status` | enum | `active`, `completed`, `cancelled` |
| `triggered_by` | string | event name |
| `started_at` | timestamp | |
| `completed_at` | timestamp nullable | |

---

## Permissions

```
crm.customer-success.view
crm.customer-success.manage-plans
crm.customer-success.configure-health
crm.customer-success.manage-playbooks
crm.customer-success.view-renewals
```

---

## Competitor Comparison

| Feature | FlowFlex | Gainsight | ChurnZero | HubSpot CS |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€€) | ❌ (€€€) | ❌ (€€€) |
| Configurable health score | ✅ | ✅ | ✅ | partial |
| Playbooks | ✅ | ✅ | ✅ | partial |
| QBR builder | ✅ | ✅ | ✅ | ❌ |
| Renewal pipeline | ✅ | ✅ | ✅ | ✅ |
| Native CRM integration | ✅ (built-in) | ❌ (Salesforce) | ❌ | ✅ (HubSpot) |

---

## Related

- [[CRM Overview]]
- [[Contact & Company Management]]
- [[Revenue Intelligence & Forecasting]]
- [[Customer Support & Helpdesk]]
- [[AI Sales Coach]]
- [[Loyalty & Retention]]
