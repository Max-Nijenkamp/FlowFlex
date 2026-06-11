---
type: module
domain: AI & Automation
domain-key: ai
panel: ai
module-key: ai.workflows
status: planned
priority: p3
depends-on: [core.billing, core.rbac, foundation.queues]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [queues, events, custom-pages]
tables: [ai_workflows, ai_workflow_runs]
permission-prefix: ai.workflows
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Workflow Builder

Visual no-code automation builder. Trigger → conditions → actions across domains. The "Zapier inside FlowFlex" — automate cross-domain processes. (No LLM dependency — automation only; named "AI" by domain placement.)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, queued execution |

(Triggers/actions per domain available only when that module is active — registry-gated.)

---

## Core Features

- Visual flow editor: trigger node → condition nodes → action nodes
- **Trigger registry** = the event-bus map ([[architecture/event-bus]]): any contracted domain event (deal won, invoice paid, employee hired, form submitted) + schedule triggers
- Conditions: branch on trigger-payload fields, AND/OR logic
- **Action registry**: domains register typed actions (create task, send notification, call webhook, wait/delay, update record) — each action validates its config + executes through the owning module's service (never raw writes)
- Cross-domain: "When deal won → create project + notify finance"
- Run history: every execution logged with input/output per node
- Error handling per action: retry (3×), stop, or continue
- Enable/disable workflows; loop guard (workflow-caused events don't re-trigger workflows *(assumed: system-actor flag, depth 1)*)
- Test mode: dry-run with sample payload

---

## Data Model

### ai_workflows — id, company_id (indexed), name, trigger (jsonb {event|schedule, config}), nodes (jsonb graph: conditions + actions, registry-validated), is_active, run_count, deleted_at
### ai_workflow_runs — id, workflow_id FK, company_id (indexed), trigger_data (jsonb), status (running/success/failed/partial), node_results (jsonb), started_at, completed_at; pruned 90 days *(assumed)*

---

## DTOs

### CreateWorkflowData — name, trigger (event in event-bus map + module active, or cron-ish schedule), nodes (graph schema-validated: reachable, no cycles, action configs per registry)

## Services & Actions

- `WorkflowTriggerListener` — universal queued listener on mapped events (like core.webhooks): finds active workflows for company + event → dispatches `RunWorkflowJob`
- `RunWorkflowJob` — walks graph: conditions on payload, actions via `WorkflowActionRegistry::execute` (per-action try/catch + error policy), node results logged
- `WorkflowActionRegistry::register(key, ActionDefinition)` — domains register in providers; execution respects module activation + CompanyScope
- `DryRunAction` — sample payload, no side effects (actions report would-do)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunWorkflowJob` | default | per trigger | run row per trigger instance; action retries per policy |
| `RunScheduledWorkflowsCommand` | default | every 15 min | next-run cursor like scheduled exports |
| `PruneWorkflowRunsCommand` | default | daily | date guard |

---

## Filament

**Nav group:** Workflows

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WorkflowBuilderPage` | #9-style node editor custom page | Livewire + Alpine/JS graph editor *(assumed: list-based builder v1, visual canvas later)* |
| `WorkflowResource` | #1 CRUD resource | enable/disable, run count |
| `WorkflowRunResource` | #1 (read-only) | per-node results |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('ai.workflows.view-any') && BillingService::hasModule('ai.workflows')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a rate limiter on outbound webhook actions (and/or per-workflow execution throttling) to prevent abuse/SSRF-amplification, in addition to the existing loop guard.

---

## Permissions

`ai.workflows.view-any` · `ai.workflows.manage` · `ai.workflows.run-test`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Event trigger runs matching active workflows for the right company only
- [ ] Conditions branch on payload fields (AND/OR fixtures)
- [ ] Each registered action type executes through owning service; inactive-module action rejected at save
- [ ] Error policies: retry/stop/continue per fixture
- [ ] Loop guard: workflow-created events don't cascade
- [ ] Dry-run produces node results without side effects
- [ ] Graph validation rejects cycles/orphans

---

## Build Manifest

```
database/migrations/xxxx_create_ai_workflows_table.php
database/migrations/xxxx_create_ai_workflow_runs_table.php
app/Models/AI/{Workflow,WorkflowRun}.php
app/Data/AI/CreateWorkflowData.php
app/Support/AI/{WorkflowActionRegistry,ActionDefinition,WorkflowGraphValidator}.php
app/Listeners/AI/WorkflowTriggerListener.php
app/Jobs/AI/RunWorkflowJob.php
app/Actions/AI/DryRunAction.php
app/Console/Commands/AI/{RunScheduledWorkflowsCommand,PruneWorkflowRunsCommand}.php
app/Filament/AI/Pages/WorkflowBuilderPage.php
app/Filament/AI/Resources/{WorkflowResource,WorkflowRunResource}.php
database/factories/AI/WorkflowFactory.php
tests/Feature/AI/{WorkflowExecutionTest,WorkflowLoopGuardTest}.php
```

---

## Related

- [[architecture/event-bus]]
- [[architecture/queue-jobs]]
- [[domains/core/webhooks]] — same universal-listener pattern
