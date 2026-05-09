---
tags: [flowflex, domain/ai-automation, ai-agents, autonomous, phase/6]
domain: AI & Automation
panel: ai
color: "#06B6D4"
status: planned
last_updated: 2026-05-08
---

# AI Agents

Autonomous background agents that run on a schedule or in response to events. They observe, decide, and act — like having a specialist on your team who never sleeps.

**Who uses it:** Admins, managers, domain specialists
**Filament Panel:** `ai`
**Depends on:** [[AI Infrastructure]], [[Workflow Automation Builder]], all domain modules
**Phase:** 6
**Build complexity:** Very High

---

## What Are AI Agents?

An AI Agent is a persistent, named background process that:
1. **Monitors** data (polls or listens to events)
2. **Reasons** about what action to take using an LLM
3. **Acts** — creates records, sends messages, triggers workflows
4. **Reports** — logs what it did and why

Unlike Workflow Automation (deterministic rules), AI Agents use natural language reasoning to handle ambiguous situations, prioritise competing tasks, and adapt to context.

---

## Pre-Built Agents

### HR Agents

**Hiring Pipeline Manager**
- Monitors candidate pipeline daily
- Surfaces stalled applications (no activity > 5 days)
- Drafts follow-up emails for hiring managers
- Flags candidates who match multiple open roles

**Onboarding Concierge**
- Follows new starters through their first 30 days
- Checks task completion, sends nudges
- Alerts HR if onboarding falls behind schedule
- Suggests next steps based on role type

**Burnout Early Warning**
- Analyses leave patterns, time-tracking data, and eNPS trends
- Flags individuals showing early stress indicators
- Suggests conversation prompts for managers
- Never acts automatically — report-only by default for privacy

### Finance Agents

**Cash Flow Watchdog**
- Monitors bank balance, receivables, and payables daily
- Alerts when projected balance drops below threshold
- Suggests which invoices to chase first
- Flags unusual transactions for review

**Invoice Collection Agent**
- Monitors overdue invoices
- Drafts and sends escalating reminder emails (day 1, day 7, day 14, day 30)
- Adapts tone based on client relationship history
- Escalates to human when client disputes

**Expense Anomaly Detector**
- Reviews submitted expenses daily
- Flags outliers (unusually high amounts, category mismatches, duplicate submissions)
- Adds a note to flagged expenses for approver review

### CRM Agents

**Deal Momentum Monitor**
- Tracks deals with no activity for N days
- Drafts suggested outreach messages for reps
- Flags deals likely to slip based on close date vs activity level
- Surfaces competitive risk signals from notes

**Lead Qualification Agent**
- Scores new inbound leads automatically
- Enriches lead data from public sources
- Routes high-value leads to senior reps immediately
- Adds qualification notes to CRM record

### Operations Agents

**Inventory Reorder Agent**
- Monitors stock levels against reorder points
- Auto-creates purchase orders when threshold hit
- Considers lead times and current open POs before ordering
- Weekly summary report to procurement team

**Maintenance Scheduler**
- Monitors equipment maintenance schedules
- Creates work orders in advance of due dates
- Considers technician availability and parts lead times
- Escalates when critical equipment approaches failure threshold

---

## Custom Agent Builder

Create your own agents with a natural language description:

1. **Name your agent** — "Revenue Alert Bot"
2. **Describe its goal** — "Monitor our monthly revenue vs target and alert the leadership team if we're tracking below 90% of target by the 15th of each month"
3. **Set data access** — which modules it can read/write
4. **Set schedule** — when to run (daily, hourly, on event)
5. **Set output** — what it reports and to whom
6. **Review AI-generated plan** — see what it will actually do before activating
7. **Activate** — agent runs autonomously from here

---

## Agent Governance

All agents operate under strict guardrails:

- **Human-in-the-loop options:** Report-only / Suggest + confirm / Autonomous (per action type)
- **Action audit log:** Every action an agent takes is logged with full reasoning trace
- **Rate limits:** Max N actions per hour/day per agent (configurable)
- **Emergency stop:** Instant deactivation from dashboard
- **Reasoning transparency:** Agent explains its decisions in plain language
- **Rollback:** Many agent actions support one-click undo

---

## Database Tables

### `ai_agents`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text | |
| `agent_type` | enum | `system`, `custom` |
| `system_agent_key` | string nullable | for pre-built agents |
| `goal_prompt` | text | what the agent is trying to achieve |
| `data_access_scopes` | json | which modules it can access |
| `schedule_cron` | string nullable | when to run |
| `trigger_events` | json nullable | events that wake it |
| `autonomy_level` | enum | `report_only`, `suggest`, `autonomous` |
| `is_active` | boolean | |
| `run_count` | integer default 0 | |
| `last_run_at` | timestamp nullable | |
| `created_by` | ulid FK | → tenants |

### `ai_agent_runs`
| Column | Type | Notes |
|---|---|---|
| `agent_id` | ulid FK | |
| `status` | enum | `running`, `completed`, `failed` |
| `observations` | json | what the agent observed |
| `reasoning` | text | agent's reasoning trace |
| `actions_taken` | json | list of actions taken |
| `actions_pending` | json | waiting for human approval |
| `summary` | text | human-readable summary |
| `started_at` | timestamp | |
| `completed_at` | timestamp nullable | |

---

## Permissions

```
ai.agents.view
ai.agents.create
ai.agents.edit
ai.agents.delete
ai.agents.activate
ai.agents.view-logs
ai.agents.approve-actions
```

---

## Related

- [[AI Overview]]
- [[AI Assistant & Copilot]]
- [[Workflow Automation Builder]]
- [[AI Infrastructure]]
