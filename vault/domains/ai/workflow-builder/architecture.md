---
domain: ai
module: workflow-builder
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workflow Builder — Architecture

See also [[_module|ai.workflows._module]], [[../../../architecture/event-bus]], [[../../../architecture/queue-jobs]], [[../../../architecture/patterns/custom-pages]], [[../../../security/data-ownership]].

No LLM anywhere in this module — it is a deterministic event/action orchestrator.

---

## Services & Actions

- **`WorkflowTriggerListener`** — a single **universal queued listener** subscribed to **all** contracted domain events ([[../../../architecture/event-bus]]), exactly like [[../../core/webhooks/_module|core.webhooks]]. On each event it: (1) resolves the acting company from the event's scalar `company_id`, (2) finds active `ai_workflows` whose `trigger` matches this event key **and** whose actions are registry-gated to that company's active modules, (3) dispatches one `RunWorkflowJob` per match. Loop guard: events carrying the *(assumed)* system-actor flag (i.e. caused by a workflow) are skipped at depth 1, so workflow-produced events never re-trigger workflows.

- **`RunWorkflowJob`** — walks the `nodes` graph:
  1. Evaluates **condition** nodes against the trigger payload (AND/OR).
  2. Executes **action** nodes via `WorkflowActionRegistry::execute(key, config, payload)` — each wrapped in its own `try/catch` with a per-action **error policy**: `retry` (3× with backoff), `stop` (halt the run → `failed`/`partial`), or `continue` (log + proceed).
  3. Writes per-node results into `ai_workflow_runs.node_results`; sets final `status` (`success`/`failed`/`partial`) and `completed_at`; increments `run_count`.

- **`WorkflowActionRegistry::register(key, ActionDefinition)`** — domains register their typed actions in their own service providers. `::execute` runs under `CompanyContext`, so every action call respects `CompanyScope` and module activation, and reaches the **owning module's service** (never a raw cross-domain write).

- **`DryRunAction`** — replays a sample payload through the graph; actions report their would-do result without side effects. Produces the same `node_results` shape for preview.

---

## Graph Validation

`WorkflowGraphValidator` runs at save time (invoked from `CreateWorkflowData`):

- Trigger node exists and maps to a known event key or valid schedule.
- Every node is **reachable** from the trigger.
- **No cycles** in the condition/action graph.
- Each action's `config` validates against its `ActionDefinition` schema, and its owning module is active for the company (else rejected at save).

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunWorkflowJob` | default | per trigger instance | one run row per trigger; action retries per policy |
| `RunScheduledWorkflowsCommand` | default | every 15 min | next-run cursor per workflow (like scheduled exports) |
| `PruneWorkflowRunsCommand` | default | daily | date guard on `started_at` (90 days *(assumed)*) |

No Meilisearch index. No realtime broadcast (run history reads on page load).

> [!warning] UNVERIFIED
> The scheduled-workflow cadence (every 15 min) and the loop-guard mechanism (system-actor flag, depth 1) are assumed. Confirm the actor-tagging approach against [[../../../architecture/event-bus]].
