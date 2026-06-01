---
type: module
domain: AI & Automation
panel: ai
module-key: ai.workflows
status: planned
color: "#4ADE80"
---

# Workflow Builder

Visual no-code automation builder. Trigger → conditions → actions across domains. The "Zapier inside FlowFlex" — automate cross-domain processes.

## Core Features

- Visual flow editor: trigger node → condition nodes → action nodes
- Triggers: domain events (deal won, invoice paid, employee hired, form submitted, schedule)
- Conditions: branch on field values, AND/OR logic
- Actions: create/update records in any domain, send email/notification, call webhook, wait/delay
- Cross-domain: e.g. "When deal won → create project + notify finance + add CRM task"
- Run history: every execution logged with input/output per node
- Error handling: retry, stop, or continue on action failure
- Enable/disable workflows
- Test mode: dry-run with sample data

## Data Model

| Table | Key Columns |
|---|---|
| `ai_workflows` | company_id, name, trigger (json), nodes (json: conditions + actions graph), is_active |
| `ai_workflow_runs` | workflow_id, company_id, trigger_data (json), status, node_results (json), started_at, completed_at |

## Filament

**Nav group:** Workflows

- `WorkflowBuilderPage` (custom page) — node-based visual flow editor (Vue component embedded)
- `WorkflowResource` — list, enable/disable
- `WorkflowRunResource` — execution history with per-node detail

## Cross-Domain / Events / Jobs

- Subscribes to domain events as triggers (see [[architecture/event-bus]])
- Actions execute via queue (see [[architecture/queue-jobs]])
- Respects CompanyScope + module activation (can't act on inactive modules)

## Related

- [[architecture/event-bus]]
- [[architecture/queue-jobs]]
- [[architecture/patterns/custom-pages]]
