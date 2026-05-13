---
type: module
domain: AI & Automation
panel: ai
module-key: ai.workflows
status: planned
color: "#4ADE80"
---

# Workflow Builder

> Visual no-code workflow automation engine — Trigger → Conditions → Actions — spanning all FlowFlex domains without writing code.

**Panel:** `ai`
**Module key:** `ai.workflows`

---

## What It Does

Workflow Builder is FlowFlex's native automation engine that replaces external tools like Zapier and Make for in-platform use cases. Users build workflows on a visual canvas by connecting a trigger event (e.g. a new support ticket is created), optional conditions (e.g. priority is High), and one or more actions (e.g. assign to a specific agent, send a Slack message, create a CRM task). Workflows are versioned, can be paused and tested without activating, and every execution is logged with full input/output data for debugging.

---

## Features

### Core
- Trigger types: record created/updated/deleted, field value change, scheduled time, webhook received, manual trigger
- Condition builder: field comparisons, AND/OR logic, nested conditions
- Action types: create/update a record, send email/notification, call a webhook, run another workflow (chain), assign to user
- Multi-action steps: execute multiple actions in a workflow branch
- Test mode: run a workflow with sample data before activating
- Execution logs: full log of every workflow run with input data, each step result, and output

### Advanced
- Branching paths: if/else forks based on condition evaluation
- Wait steps: pause execution for a defined time or until a condition is met
- Loop steps: iterate over a list of related records
- Error handling: define fallback steps when an action fails
- Versioning: save new versions of a workflow; roll back to a previous version
- Team-shared workflows: publish a workflow template for other teams to clone

### AI-Powered
- Natural language workflow creation: describe what you want in plain English, AI generates the initial workflow structure
- Anomaly detection integration: AI-detected anomalies can automatically trigger a workflow
- Workflow optimisation suggestions: AI flags redundant or inefficient steps in existing workflows

---

## Data Model

```erDiagram
    workflows {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string status
        integer version
        json trigger_config
        json steps
        ulid created_by FK
        timestamps created_at_updated_at
    }

    workflow_executions {
        ulid id PK
        ulid workflow_id FK
        ulid company_id FK
        string trigger_type
        json trigger_data
        string status
        json step_logs
        timestamp started_at
        timestamp completed_at
    }

    workflows ||--o{ workflow_executions : "executed as"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `workflows` | Workflow definitions | `id`, `company_id`, `name`, `status`, `trigger_config`, `steps` |
| `workflow_executions` | Execution history | `id`, `workflow_id`, `status`, `trigger_data`, `step_logs`, `started_at` |

---

## Permissions

```
ai.workflows.view-any
ai.workflows.create
ai.workflows.update
ai.workflows.delete
ai.workflows.activate
```

---

## Filament

- **Resource:** None (custom page only)
- **Pages:** N/A
- **Custom pages:** `WorkflowBuilderPage` (full-screen visual canvas editor), `WorkflowListPage`, `WorkflowExecutionLogPage`
- **Widgets:** `ActiveWorkflowsWidget`, `ExecutionErrorsWidget`
- **Nav group:** Workflows

---

## Displaces

| Feature | FlowFlex | Zapier | Make (Integromat) | n8n |
|---|---|---|---|---|
| No-code visual builder | Yes | Yes | Yes | Yes |
| Native FlowFlex data access | Yes | No | No | No |
| Execution logs in platform | Yes | Yes | Yes | Yes |
| Natural language creation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Filament:** `WorkflowBuilderPage` is the highest-complexity custom UI in the AI domain — a full-screen visual canvas with a node-and-edge graph editor. This cannot be built with Filament form or table components. Use a JavaScript flow/graph library:
- **ReactFlow** (MIT for basic usage) — React-based, not Vue — requires an embedded React component in the Filament page. Complexity: high.
- **Vue Flow** (MIT) — Vue 3 native, maps well to the Livewire/Alpine.js stack. Recommended.
- **Plain Canvas with jsPlumb** — lower-level but framework-agnostic.

**Recommended:** Vue Flow embedded as a custom Blade view in the `WorkflowBuilderPage`. Workflow data (`workflows.steps` JSON) is loaded via a Livewire `$steps` property and passed to the Vue Flow component as initial node/edge data. Saving posts the updated steps JSON back to Livewire via a `saveWorkflow(string $stepsJson)` action.

**Workflow execution engine:** The execution engine runs as a queued Laravel job `ExecuteWorkflowJob`. It reads `workflows.steps` JSON (which is a flat array of step objects: `{id, type, config, next_step_ids}`), processes each step in sequence or branches in parallel using `Bus::chain()` or `Bus::batch()`. The engine is stateless — all state is in `workflow_executions.step_logs` JSON, written after each step completes.

**Trigger registration:** Domain modules register workflow triggers in their ServiceProvider: `WorkflowTriggerRegistry::register('crm.deal.created', ['label' => 'Deal created', 'payload' => CrmDealPayload::class])`. The Workflow Builder UI reads available triggers from the registry. When a trigger event fires (e.g. a `DealCreated` Eloquent event), the `WorkflowDispatchListener` queries `workflows` for active entries matching that trigger and dispatches `ExecuteWorkflowJob` for each.

**Wait steps:** A wait step pauses the execution by storing the job ID in `workflow_executions.step_logs` and scheduling a `ResumeWorkflowJob` via `dispatch(...)->delay($waitDuration)`. Laravel's delayed dispatch uses Redis — no external scheduler needed.

**Natural language workflow creation:** Calls `app/Services/AI/WorkflowGeneratorService.php`. The user describes the workflow in plain English; the service sends the description to OpenAI GPT-4o with a JSON schema system prompt. The response is a `workflows.steps` JSON array that is loaded into the builder canvas for the user to review before saving.

**Test mode:** The builder's "Run test" button dispatches `ExecuteWorkflowJob` with `$dry_run = true`. In dry run mode, actions are logged but not executed (no emails sent, no records created). The execution log is displayed in the builder UI immediately.

## Related

- [[copilot]] — Copilot can trigger workflows from suggestions
- [[anomaly-detection]] — anomalies can fire workflow triggers
- [[ai/INDEX]] — AI domain overview
