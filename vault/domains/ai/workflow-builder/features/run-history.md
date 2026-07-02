---
domain: ai
module: workflow-builder
feature: run-history
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Run History

The read-only audit trail of every workflow execution: which workflow fired, on what payload, whether it succeeded, and the per-node input/output/error trace. This is what makes the automation explainable and debuggable.

## Behaviour

- One `ai_workflow_runs` row per trigger instance, written by `RunWorkflowJob` as it walks the graph.
- Records `trigger_data`, final `status` (`running` / `success` / `failed` / `partial`), and `node_results` (per-node input/output/error).
- `partial` = some actions succeeded, others hit a `stop`/`continue` error policy.
- Append-only; pruned at 90 days *(assumed)*.
- Read-only surface — nothing here mutates a workflow or re-runs it *(re-run is out of scope for v1 — see [[../unknowns]])*.

## UI

- **Kind**: simple-resource   <!-- WorkflowRunResource, read-only list; per-run detail is a custom page -->
- **Page**: "Run history" (`/app/ai/workflows/runs`) *(route slug assumed)*; per-run detail at `/app/ai/workflows/runs/{id}`.
- **Layout**: table (workflow name, status badge, started-at, duration); row → detail page showing the trigger payload + an ordered per-node trace (each node's input, output, error, and which error policy fired).
- **Key interactions**: filter by workflow / status / date; open a run → node-by-node trace; copy a failed node's error.
- **States**: empty (no runs yet → "runs will appear here once a workflow fires") · loading (skeleton table) · error (toast + retry) · selected (run row → detail page open).
- **Gating**: `ai.workflows.view-any`.

## Data

- Owns / writes: `ai_workflow_runs` is written by `RunWorkflowJob`; this feature only **reads** it.
- Reads: `ai_workflow_runs` + parent `ai_workflows.name`.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: run rows produced by [[trigger-registry|Trigger Registry]] → `RunWorkflowJob` executing [[action-registry|Action Registry]] actions.
- Shared entity: none.

## Unknowns

> [!warning] UNVERIFIED
> The 90-day prune horizon and the `/app/ai/workflows/runs` route slug are assumed; whether a failed run can be re-run from history is unspecced. See [[../unknowns]].

## Related

- [[../_module|Workflow Builder]] · [[flow-editor|Flow Editor]] · [[trigger-registry|Trigger Registry]] · [[action-registry|Action Registry]]
- [[../data-model]] · [[../../../../architecture/data-lifecycle]]
