---
domain: ai
module: workflow-builder
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workflow Builder

No-code cross-domain automation builder — the "Zapier inside FlowFlex". A company admin wires **trigger → conditions → actions** to automate processes across domains ("when deal won → create project + notify finance"). **No LLM dependency** — this module is pure event/action orchestration; it is named "AI" only by domain placement in `/ai`, never by using a model.

## Module-key

| Field | Value |
|---|---|
| key | `ai.workflows` |
| priority | p3 |
| panel | ai |
| permission-prefix | `ai.workflows` |
| tables | `ai_workflows`, `ai_workflow_runs` |
| encrypted-fields | none |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()` |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | Queued trigger listener + run execution |

Triggers and actions for a given domain are available **only when that domain module is active** — the registries are gated to the company's active-module set.

## Core Features

- **Flow editor** — trigger node → condition nodes → action nodes; enable/disable per workflow.
- **Trigger registry** = the event-bus map ([[../../../architecture/event-bus]]): any contracted domain event (deal won, invoice paid, employee hired, form submitted) + schedule triggers.
- **Conditions** — branch on trigger-payload fields with AND/OR logic.
- **Action registry** — domains register typed actions (create task, send notification, call webhook, wait/delay, update record); each action validates its config and **executes through the owning module's service** — never a raw write into another domain's tables.
- **Run history** — every execution logged with input/output per node.
- **Error handling per action** — retry (3×), stop, or continue.
- **Loop guard** — workflow-caused events don't re-trigger workflows *(assumed: system-actor flag, depth 1)*.
- **Test mode** — dry-run with a sample payload; actions report would-do, no side effects.

## See features/

- [[features/flow-editor|Flow Editor]] — the node builder + `WorkflowResource` (enable/disable, run count).
- [[features/trigger-registry|Trigger Registry]] — event-bus map + schedule triggers; surfaces as the trigger picker.
- [[features/action-registry|Action Registry]] — typed `ActionDefinition`s domains register; executes via owning service.
- [[features/run-history|Run History]] — read-only run list + per-run node-result detail.

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Event trigger runs matching active workflows for the right company only
- [ ] Conditions branch on payload fields (AND/OR fixtures)
- [ ] Each registered action type executes through owning service; inactive-module action rejected at save
- [ ] Error policies: retry/stop/continue per fixture
- [ ] Loop guard: workflow-created events don't cascade
- [ ] Dry-run produces node results without side effects
- [ ] Graph validation rejects cycles/orphans

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | **every** contracted domain event ([[../../../architecture/event-bus]]) | crm, finance, hr, projects, forms, … | universal trigger listener (like [[../../core/webhooks/_module\|core.webhooks]]); registry-gated to the company's active modules |
| Provides | *(nothing external)* | — | fires no domain events others consume; loop-guarded system-actor events *(assumed)* |
| Actions | command calls into owning services (create task, notify, webhook, update record) | crm / finance / hr / … services | via `WorkflowActionRegistry`; respects module-activation + `CompanyScope` — never raw table writes |

**Data ownership:** `ai.workflows` writes **only** `ai_workflows` and `ai_workflow_runs`. Every cross-domain effect goes through the owning module's service (never that domain's tables) — [[../../../security/data-ownership]].

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../core/webhooks/_module|core.webhooks]] — same universal-listener pattern
- [[../../../architecture/event-bus]] · [[../../../architecture/queue-jobs]]
- [[../model-config/_module|Model Config]] — sibling `/ai` module (this one has no LLM tie)
