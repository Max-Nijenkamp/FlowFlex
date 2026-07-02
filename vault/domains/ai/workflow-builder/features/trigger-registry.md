---
domain: ai
module: workflow-builder
feature: trigger-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Trigger Registry

The set of things that can start a workflow: every contracted domain event (deal won, invoice paid, employee hired, form submitted) plus schedule triggers. It *is* the event-bus map, gated to the company's active modules.

## Behaviour

- The trigger surface = the event-bus contract map ([[../../../../architecture/event-bus]]) + schedule triggers — no per-domain glue.
- `WorkflowTriggerListener` is one **universal queued listener** subscribed to all contracted events; on each event it resolves the company from the scalar `company_id`, finds matching active workflows, and dispatches `RunWorkflowJob` per match.
- Event triggers offered in the editor are gated to modules the company has active.
- Schedule triggers are evaluated by `RunScheduledWorkflowsCommand` (every 15 min *(assumed)*, next-run cursor per workflow).
- Loop guard: workflow-produced events (system-actor flagged *(assumed)*) are skipped so they don't re-trigger.

## UI

- **Kind**: background   <!-- a registry + universal listener; no screen of its own -->
- Surfaces inside [[flow-editor|Flow Editor]] as the **trigger picker** (choose an event or a schedule).

## Data

- Owns / writes: nothing directly (the listener dispatches jobs; runs are written by `RunWorkflowJob` into `ai_workflow_runs`).
- Reads: the event-bus map; active-module set (core.billing).
- Cross-domain writes: none — it only *listens* to other domains' events ([[../../../../security/data-ownership]]).

## Relations

- Consumes: **every** contracted domain event from crm / finance / hr / projects / forms / … ([[../../../../architecture/event-bus]]).
- Feeds: matched triggers → `RunWorkflowJob` → [[run-history|Run History]].
- Shared entity: event contracts owned by the event bus; `company_id` scalar on every event.

## Unknowns

> [!warning] UNVERIFIED
> The loop-guard mechanism (system-actor flag, depth 1) and the 15-min scheduled cadence are assumed. Confirm event actor-tagging against [[../../../../architecture/event-bus]]. See [[../unknowns]].

## Related

- [[../_module|Workflow Builder]] · [[flow-editor|Flow Editor]] · [[action-registry|Action Registry]] · [[run-history|Run History]]
- [[../../../../architecture/event-bus]] · [[../../../core/webhooks/_module|core.webhooks]] (same pattern)
