---
domain: ai
module: workflow-builder
feature: action-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Action Registry

The set of things a workflow can *do*: typed actions that domains register (create task, send notification, call webhook, wait/delay, update record). Every action executes through the owning module's service — never a raw cross-domain write.

## Behaviour

- `WorkflowActionRegistry::register(key, ActionDefinition)` — each domain registers its typed actions in its own provider. An `ActionDefinition` declares `key`, `owning_module`, a `config` schema, and the service call to perform.
- `WorkflowActionRegistry::execute(key, config, payload)` runs under `CompanyContext`, invokes the **owning module's service** (e.g. `TaskService::create`, `NotificationService::send`, `WebhookService::call`, `RecordService::update`), and returns a `NodeResult` logged into `ai_workflow_runs.node_results`.
- Module-activation + `CompanyScope` enforced by the owning service; an inactive-module action is rejected at save.
- Per-action error policy (retry 3× / stop / continue) governs partial-failure behaviour.

## UI

- **Kind**: background   <!-- a registry + execution path; no screen of its own -->
- Surfaces inside [[flow-editor|Flow Editor]] as the **action picker** (choose an action, fill its config).

## Data

- Owns / writes: nothing of its own (results are written by `RunWorkflowJob` into `ai_workflow_runs`).
- Reads: the registered `ActionDefinition`s; active-module set.
- Cross-domain writes: **via the owning module's service only** — the created task / notification / updated record is owned and written by that module, never by workflow-builder ([[../../../../security/data-ownership]]).

## Relations

- Feeds (command calls): `TaskService::create`, `NotificationService::send`, `WebhookService::call`, `RecordService::update`, … across crm / finance / hr / projects / comms services.
- Shared entity: each action's `owning_module` permission set + validation live in that module.

## Test Checklist

### Unit
- [ ] `ActionDefinition` config schema validation rejects malformed config
- [ ] Error policies map correctly: retry (3× backoff), stop (run failed/partial), continue (log + proceed)

### Feature (Pest)
- [ ] `execute()` runs under `CompanyContext` and calls the owning module's service — no direct cross-domain table writes (arch assertion)
- [ ] Inactive-module action rejected at save; webhook action refuses internal/loopback targets (SSRF guard)
- [ ] Dry-run returns node results with zero side effects

## Unknowns

> [!warning] UNVERIFIED
> The exact v1 action set (create task, notify, webhook, wait/delay, update record) is assumed, as is webhook SSRF hardening (block internal/loopback targets). See [[../unknowns]] + [[../security]].

## Related

- [[../_module|Workflow Builder]] · [[flow-editor|Flow Editor]] · [[trigger-registry|Trigger Registry]] · [[run-history|Run History]]
- [[../api]] · [[../../../../security/data-ownership]]
