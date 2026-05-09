---
type: concept
category: architecture
phase: 3
last_updated: 2026-05-09
---

# Workflow Rules (Trigger-Action Automation)

No-code automation: "When [trigger] happens, if [conditions], then do [actions]." Like Salesforce Workflow Rules or HubSpot Workflows (simple tier). Not to be confused with the AI domain's complex automation sequences.

---

## Why Phase 3 (Not Later)

Phase 3 customers start hitting operational friction without automation:
- "When deal closes, create onboarding project" — manual today
- "When invoice overdue 7 days, send reminder email" — manual today
- "When ticket resolved, send CSAT survey" — manual today

This is basic CRM/ERP functionality. Zapier exists only because platforms don't have this built in. FlowFlex should eliminate Zapier for internal use cases.

---

## Trigger Types

| Trigger | Example |
|---|---|
| Record created | Contact created |
| Record updated | Deal stage changed |
| Field changed | Invoice status → overdue |
| Date reached | 7 days before contract_end_date |
| Event fired | `CheckoutCompleted`, `EmployeeHired` |
| Webhook received | External system sends webhook |
| Scheduled | Every Monday at 09:00 |

---

## Condition Logic

```
IF deal.amount > 50000
AND deal.stage = "Proposal Sent"
AND deal.owner.role = "Account Executive"
```

AND / OR logic, nested groups.  
Field comparisons: `=`, `≠`, `>`, `<`, `contains`, `is empty`, `is set`.

---

## Action Types

| Action | Example |
|---|---|
| Send email | Trigger welcome email via Mailgun |
| Send notification | Push notification to assigned user |
| Create record | Create task, create project from template |
| Update field | Set status = "Active", set owner = [user] |
| Assign to user | Auto-assign based on round-robin or territory |
| Add tag | Tag contact as "Hot Lead" |
| Webhook outbound | POST to external URL with payload |
| Delay | Wait 3 days, then continue |
| Branch | If [condition] → action A, else → action B |

---

## Architecture

```sql
CREATE TABLE automation_workflows (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100),
    trigger_type    VARCHAR(50),
    trigger_config  JSON,       -- entity_type, event, schedule
    conditions      JSON,       -- condition groups + operators
    actions         JSON,       -- ordered list of action definitions
    is_active       BOOLEAN DEFAULT FALSE,
    run_count       BIGINT DEFAULT 0,
    last_run_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE automation_workflow_runs (
    id              ULID PRIMARY KEY,
    workflow_id     ULID NOT NULL REFERENCES automation_workflows(id),
    trigger_data    JSON,       -- snapshot of triggering record
    status          ENUM('running','completed','failed','skipped'),
    steps_completed INT DEFAULT 0,
    error_message   TEXT NULL,
    started_at      TIMESTAMP DEFAULT NOW(),
    completed_at    TIMESTAMP NULL
);
```

---

## UI Builder

Visual rule builder (not code):

```
┌─ TRIGGER ──────────────────────────┐
│ When: Deal → Stage Changed         │
│ To: "Closed Won"                   │
└────────────────────────────────────┘
         │
┌─ CONDITIONS ───────────────────────┐
│ Deal Amount > €10,000              │
│ AND Account Type = "Enterprise"    │
└────────────────────────────────────┘
         │
┌─ ACTIONS ──────────────────────────┐
│ 1. Create Project from Template    │
│    Template: "Enterprise Onboarding"│
│ 2. Send Email to Deal Owner        │
│    Template: "New Deal Won — Action"│
│ 3. Notify: Customer Success Team   │
└────────────────────────────────────┘
```

---

## Distinction from AI Automation

This module: **simple, deterministic, no-code rules**. No AI.  
AI domain ([[MOC_AI]]): LLM-powered sequences, multi-step AI agents, intelligent branching.  
Both coexist — simple workflows use this; complex ones use AI domain.

---

## Phase Priority

**Phase 3.** Required before CRM, Finance, and HR can be truly self-service for operations teams.

---

## Related

- [[concept-custom-objects]] — workflows can trigger on custom object changes
- [[concept-event-driven]] — workflows consume platform events
- [[MOC_AI]] — AI-powered complex automation
