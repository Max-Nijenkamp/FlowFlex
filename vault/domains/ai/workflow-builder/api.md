---
domain: ai
module: workflow-builder
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workflow Builder — API / DTOs

The module exposes **no external HTTP surface**. Its "API" is one input DTO plus the in-process registry command path. Cross-domain effects are always command calls into owning services, never direct writes ([[../../../security/data-ownership]]).

---

## CreateWorkflowData (input)

Written by the flow editor.

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `trigger` | array | `{ type: event\|schedule, config }`; event key must exist in the event-bus map **and** its source module active; schedule = cron-ish config |
| `nodes` | array | graph schema-validated by `WorkflowGraphValidator`: reachable, no cycles, each action `config` valid per its `ActionDefinition`, owning module active |
| `is_active` | bool | default false |

Validation failures (unknown event, inactive-module action, cycle/orphan) are rejected **at save**, never at run time.

---

## WorkflowActionRegistry (command API — the cross-domain execution path)

`WorkflowActionRegistry::execute(string $key, array $config, array $payload): NodeResult`

- **Registration**: `::register($key, ActionDefinition)` — each domain registers its typed actions in its own provider. An `ActionDefinition` declares: `key`, `owning_module`, `config` schema, and the service call to perform.
- **Execution**: runs under `CompanyContext`; the action invokes the **owning module's service** (e.g. `TaskService::create`, `NotificationService::send`, `WebhookService::call`, `RecordService::update`). Module-activation + `CompanyScope` are enforced by that service.
- **Never** writes another domain's tables directly.
- Returns a `NodeResult` { status, output, error } logged into `ai_workflow_runs.node_results`.

`DryRunAction::preview(Workflow, samplePayload): array` — the same graph walk with actions in report-only mode (no side effects).

---

## Public / Portal Endpoints

None. Internal `/ai` builder + an in-process registry API. No external HTTP routes, no portal surface.
