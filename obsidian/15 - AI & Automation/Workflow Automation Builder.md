---
tags: [flowflex, domain/ai-automation, automation, workflows, phase/6]
domain: AI & Automation
panel: ai
color: "#06B6D4"
status: planned
last_updated: 2026-05-08
---

# Workflow Automation Builder

No-code automation engine built into FlowFlex. Replace Zapier, Make, and n8n for anything that lives inside the platform. Works across every domain with zero latency and no per-task billing.

**Who uses it:** Admins, operations managers, HR, finance — anyone who wants to eliminate manual repetitive work
**Filament Panel:** `ai`
**Depends on:** Core, all domain modules (triggers/actions come from each domain)
**Phase:** 6
**Build complexity:** Very High — 4 resources, 3 pages, 8 tables

---

## Features

### Visual Workflow Builder

- Drag-and-drop canvas — nodes connected by lines, left-to-right flow
- Node types: Trigger, Condition, Action, Wait, Loop, AI Step, Sub-workflow
- Full-screen editor with mini-map for complex workflows
- Live execution preview — test a workflow without saving using sample data
- Version history — every saved version stored, rollback with one click
- Templates library — 50+ pre-built workflow templates for common use cases

### Trigger Types

**Time-based:**
- On schedule (cron: every hour, daily at 9am, first Monday of month, etc.)
- On date field (e.g. "3 days before contract expiry date")
- On relative date (e.g. "60 days after employee start date")

**Event-based (from any FlowFlex domain):**
- Record created / updated / deleted in any module
- Field value changed to specific value
- Status changed
- Form submitted
- File uploaded
- Payment received / overdue
- Task completed / overdue
- Employee hired / offboarded
- Deal stage changed
- Any custom event from [[Integration Hub]]

**Webhook-based:**
- Inbound webhook (receive data from external systems)
- Scheduled HTTP poll (check an external URL periodically)

### Condition Nodes

- If/else branching with nested conditions
- Operators: equals, not equals, contains, greater than, less than, is empty, is not empty, in list
- Combine conditions with AND / OR
- Delay conditions (wait until a condition becomes true, up to 30 days)

### Action Types

**FlowFlex native actions:**
- Create a record (any module)
- Update a record (field-level)
- Delete / archive a record
- Send in-app notification to a user or role
- Send email (using email template or AI-generated body)
- Send SMS (via Twilio)
- Create a task with assignee and due date
- Post a message to a chat channel
- Generate a document from a template
- Start an onboarding flow
- Trigger a payroll deduction
- Create an invoice
- Add a tag / label
- Run another workflow (sub-workflow call)

**AI-powered actions:**
- Summarise a record with AI
- Classify / categorise with AI (e.g. "Is this support ticket urgent?")
- Draft an email reply with AI
- Extract data from uploaded document with AI
- Generate a report narrative with AI

**Integration actions (via [[Integration Hub]]):**
- HTTP request to any URL (REST/GraphQL)
- Send data to Slack / Teams / Discord
- Create record in Google Sheets
- Send to Salesforce / HubSpot
- Post to any webhook

### Wait / Delay Nodes

- Wait for fixed duration (1 hour, 2 days, 1 week)
- Wait until specific datetime
- Wait until condition is met (polling)
- Wait for human approval (workflow pauses, sends approval request, resumes on approve/reject)

### Loop Nodes

- For-each loop over a list of records
- Parallel execution option (process all in parallel vs sequentially)
- Loop limit guard (max iterations per run to prevent runaway loops)
- Break condition (exit loop early if condition met)

### Workflow Testing

- Manual trigger with custom test payload
- Execution log showing each node's input/output
- Step-by-step debugger
- Dry run mode (executes logic but skips writes)

### Monitoring & Reliability

- Execution history per workflow (last 1,000 runs)
- Error log with full stack context per failed step
- Auto-retry on transient failures (3 attempts with exponential backoff)
- Dead-letter queue for permanently failed runs (manual retry available)
- Workflow health dashboard (success rate, avg duration, error rate)
- Email alert when a workflow fails N times consecutively

### Access Control

- Workflows owned by a user or shared to a role
- Shared workflows: read-only vs editable
- Workflow approval flow: require admin sign-off before activating
- Audit log entry for every workflow activation/deactivation

---

## Pre-Built Workflow Templates

| Template | Trigger | Actions |
|---|---|---|
| New hire welcome sequence | Employee created | Create onboarding tasks, send welcome email, assign IT equipment request |
| Contract expiry reminder | 30/14/7 days before expiry | Notify legal team, send renewal email to counterparty |
| Invoice overdue escalation | Invoice 7 days past due | Send reminder email, notify account manager, escalate at 14 days |
| Lead assigned notification | CRM deal assigned | Notify assignee via chat, create follow-up task for next day |
| Support ticket SLA breach | Ticket open > SLA hours | Escalate to senior tier, notify manager |
| Expense approval flow | Expense submitted | Route to manager, approve/reject, notify submitter, post to payroll |
| Offboarding checklist trigger | Employee status → terminating | Create checklist tasks for IT, HR, Finance, manager |
| Birthday celebration post | Employee birthday | Post to #general channel, send personal message from HR bot |
| Project milestone invoice | Milestone marked complete | Create invoice for milestone amount, notify billing contact |
| Inventory reorder alert | Stock level < threshold | Create purchase order, notify procurement, notify ops manager |

---

## Database Tables (8)

> All tables include standard columns: `id` (ULID PK), `company_id` FK, `deleted_at`, `created_at`, `updated_at`.

### `automation_workflows`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `is_active` | boolean default false | |
| `trigger_type` | enum | `schedule`, `event`, `webhook`, `manual` |
| `trigger_config` | json | trigger-specific configuration |
| `canvas_data` | json | full visual canvas node/edge positions |
| `version` | integer default 1 | incremented on each save |
| `created_by` | ulid FK | → tenants |
| `last_run_at` | timestamp nullable | |
| `run_count` | integer default 0 | |
| `error_count` | integer default 0 | |

### `automation_nodes`
| Column | Type | Notes |
|---|---|---|
| `workflow_id` | ulid FK | → automation_workflows |
| `node_type` | enum | `trigger`, `condition`, `action`, `wait`, `loop`, `ai_step`, `sub_workflow` |
| `node_key` | string | unique within workflow, used for edge references |
| `config` | json | node-specific configuration |
| `position_x` | integer | canvas x position |
| `position_y` | integer | canvas y position |

### `automation_edges`
| Column | Type | Notes |
|---|---|---|
| `workflow_id` | ulid FK | → automation_workflows |
| `source_node_key` | string | |
| `target_node_key` | string | |
| `condition_branch` | string nullable | `true`, `false`, `default` |

### `automation_runs`
| Column | Type | Notes |
|---|---|---|
| `workflow_id` | ulid FK | |
| `trigger_data` | json | payload that triggered the run |
| `status` | enum | `running`, `completed`, `failed`, `cancelled` |
| `started_at` | timestamp | |
| `completed_at` | timestamp nullable | |
| `duration_ms` | integer nullable | |
| `error_message` | text nullable | |

### `automation_run_steps`
| Column | Type | Notes |
|---|---|---|
| `run_id` | ulid FK | → automation_runs |
| `node_key` | string | |
| `status` | enum | `pending`, `running`, `completed`, `failed`, `skipped` |
| `input` | json nullable | |
| `output` | json nullable | |
| `error` | text nullable | |
| `duration_ms` | integer nullable | |

### `automation_templates`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text | |
| `category` | string | `hr`, `finance`, `crm`, `operations`, etc. |
| `canvas_data` | json | |
| `is_system` | boolean | system templates vs user-saved |

### `automation_webhooks`
| Column | Type | Notes |
|---|---|---|
| `workflow_id` | ulid FK | → automation_workflows |
| `webhook_key` | string unique | URL-safe random key |
| `secret` | string | HMAC signing secret |
| `last_received_at` | timestamp nullable | |

### `automation_approvals`
| Column | Type | Notes |
|---|---|---|
| `run_id` | ulid FK | → automation_runs |
| `node_key` | string | |
| `approver_id` | ulid FK | → tenants |
| `status` | enum | `pending`, `approved`, `rejected` |
| `comment` | text nullable | |
| `responded_at` | timestamp nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `AutomationTriggered` | `workflow_id`, `run_id`, `trigger_data` | Analytics, Audit Log |
| `AutomationCompleted` | `workflow_id`, `run_id`, `duration_ms` | Analytics |
| `AutomationFailed` | `workflow_id`, `run_id`, `error_message` | Notifications (admin), IT |
| `ApprovalRequested` | `run_id`, `approver_id`, `context` | Notifications |
| `ApprovalResponded` | `run_id`, `approved: bool` | Workflow engine (resume) |

---

## Permissions

```
ai.automations.view
ai.automations.create
ai.automations.edit
ai.automations.delete
ai.automations.activate
ai.automations.run-manual
ai.automations.view-logs
ai.automations.approve
ai.automation-templates.view
ai.automation-templates.create
```

---

## Competitor Comparison

| Feature | FlowFlex | Zapier | Make | n8n |
|---|---|---|---|---|
| Native data access (no API calls) | ✅ | ❌ | ❌ | ❌ |
| Per-task billing | ❌ (none) | ❌ (yes, expensive) | ❌ (yes) | ✅ (self-hosted free) |
| Visual canvas builder | ✅ | ❌ (linear) | ✅ | ✅ |
| AI action steps | ✅ | ✅ (extra cost) | ✅ (extra cost) | ✅ |
| Human-in-the-loop approvals | ✅ | ❌ | ❌ | ✅ |
| Approval workflows | ✅ | ❌ | ❌ | ✅ |
| No-latency cross-module | ✅ | ❌ (API round-trips) | ❌ | ❌ |
| Pre-built templates for business | ✅ (50+) | ✅ | ✅ | ✅ |
| Self-hosted option | planned | ❌ | ❌ | ✅ |

## Why FlowFlex Wins

Zapier and Make solve the problem of **connecting separate tools**. FlowFlex eliminates the separate tools entirely. Automations run on live internal data — no API keys, no rate limits, no per-task cost. A "new employee → create onboarding tasks" workflow in Zapier requires BambooHR + Jira API credentials, per-task billing, and breaks when either API changes. In FlowFlex it's 3 clicks and zero ongoing cost.

---

## Related

- [[AI Overview]]
- [[AI Agents]]
- [[Integration Hub]]
- [[Smart Notifications & Triggers]]
- [[Cross-Module Event Map]]
