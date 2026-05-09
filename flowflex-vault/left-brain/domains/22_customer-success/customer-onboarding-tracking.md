---
type: module
domain: Customer Success
panel: cs
phase: 5
status: planned
cssclasses: domain-cs
migration_range: 970300–970499
last_updated: 2026-05-09
---

# Customer Onboarding Tracking

Structured onboarding milestone plans per customer. Track progress from signed → fully activated. Onboarding completion = first indicator of long-term retention.

---

## Onboarding Plan

Created from a template when a new customer is signed:

### Milestone Structure
```
Onboarding Plan: "Enterprise SaaS Onboarding"
  Phase 1 — Technical Setup (Week 1–2)
    ☐ Admin user created and logged in
    ☐ SSO configured
    ☐ Team members invited (≥ 5 users)
    ☐ First data import completed

  Phase 2 — Core Feature Activation (Week 2–4)
    ☐ Primary workflow configured
    ☐ First live transaction processed
    ☐ Integration connected (CRM / Finance)
    ☐ 3 power users identified

  Phase 3 — Adoption (Week 4–8)
    ☐ Monthly active users ≥ 80% of licensed seats
    ☐ First report/dashboard created by customer
    ☐ Onboarding call #3 completed
    ☐ Go-live sign-off from customer
```

---

## Milestone Completion Triggers

Milestones can be completed:
- **Manually** — CSM marks complete (based on call or customer confirmation)
- **Automatically** — triggered by system event:
  - "First login" milestone → fires when customer user first authenticates
  - "Integration connected" → fires when integration event received from PLG
  - "First transaction" → fires when relevant Finance/CRM record created

Auto-completion reduces CSM admin burden and improves accuracy.

---

## Onboarding Health

Separate from account health score, but feeds it:
- Days since sign → expected phase
- Milestones completed on time vs late
- **Time to First Value (TTFV)**: days from sign to first core action
- **Time to Fully Onboarded**: days from sign to all milestones complete
- Benchmark: compare this customer's pace to cohort average

---

## Data Model

### `cs_onboarding_plans`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_company_id | ulid | FK |
| template_id | ulid | FK `cs_onboarding_templates` |
| csm_id | ulid | FK `employees` |
| started_at | date | |
| target_completion_date | date | |
| completed_at | date | nullable |
| status | enum | active/completed/at_risk/paused |

### `cs_onboarding_milestones`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| plan_id | ulid | FK |
| phase_name | varchar(100) | |
| title | varchar(300) | |
| completion_type | enum | manual/auto |
| auto_trigger_event | varchar(100) | nullable |
| due_days_from_start | int | |
| completed_at | timestamp | nullable |
| completed_by | ulid | nullable FK `employees` |
| notes | text | nullable |

---

## Migration

```
970300_create_cs_onboarding_templates_table
970301_create_cs_onboarding_plans_table
970302_create_cs_onboarding_milestones_table
```

---

## Related

- [[MOC_CustomerSuccess]]
- [[customer-health-scoring]] — onboarding progress feeds health
- [[cs-playbooks-alerts]] — milestone overdue → playbook trigger
- [[MOC_PLG]] — product events auto-complete milestones
