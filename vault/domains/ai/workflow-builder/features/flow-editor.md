---
domain: ai
module: workflow-builder
feature: flow-editor
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Flow Editor

Where an admin authors an automation: pick a trigger, add condition nodes, add action nodes, and enable it. Saving validates the graph so a live workflow is always structurally sound.

## Behaviour

- Build the `nodes` graph: one trigger → condition nodes (AND/OR on payload fields) → action nodes (each with a config + error policy).
- `WorkflowGraphValidator` runs at save: trigger exists, every node reachable, no cycles, each action config valid + its owning module active — else rejected at save.
- Enable/disable per workflow (`is_active`); `WorkflowResource` shows run count.
- Actions and triggers offered are gated to the company's active modules.

## UI

- **Kind**: custom-page   <!-- #9-style node editor; v1 may be a list-based builder -->
- **Page**: "Workflow builder" (`/app/ai/workflows/builder`) *(route slug assumed)*
- **Layout**: canvas/list — trigger node at top; condition + action nodes below with connectors; right rail = node config panel (pick event/action, set config, choose error policy). A separate `WorkflowResource` table lists all workflows (name, active toggle, run count).
- **Key interactions**: add node → pick from the (module-gated) trigger/action picker → configure; connect nodes; save → graph validated; toggle active; open a workflow → edit.
- **States**: empty (no workflows → "create your first automation" CTA) · loading (saving/validating) · error (invalid graph → inline node errors: cycle, orphan, inactive-module action) · selected (node selected → config panel open).
- **Gating**: `ai.workflows.view-any` to view; `ai.workflows.manage` to edit/save/enable.

## Data

- Owns / writes: `ai_workflows` (this module's own table).
- Reads: the active-module set (core.billing) + event-bus map to populate pickers; action definitions from the registry.
- Cross-domain writes: none at authoring time ([[../../../../security/data-ownership]]).

## Relations

- Uses: [[trigger-registry|Trigger Registry]] (trigger picker) + [[action-registry|Action Registry]] (action picker).
- Feeds: saved workflows drive [[run-history|Run History]] once they fire.
- Shared entity: event keys owned by the [[../../../../architecture/event-bus|event bus]].

## Test Checklist

### Unit
- [ ] `WorkflowGraphValidator` rejects cycles, orphan nodes, unknown trigger keys
- [ ] Action config invalid against its `ActionDefinition` schema rejected at save

### Feature (Pest)
- [ ] Save persists the nodes graph; action owned by an inactive module rejected at save
- [ ] Trigger/action pickers offer only the company's active-module set
- [ ] Toggle `is_active` enables/disables firing

### Livewire
- [ ] Builder renders trigger/condition/action rows; invalid graph shows inline node errors
- [ ] Edit/save denied without `ai.workflows.manage`

## Unknowns

> [!warning] UNVERIFIED
> Whether v1 is a visual drag-canvas or a list-based builder is unresolved (large front-end cost difference), and the route slug is assumed. See [[../unknowns]]. Per [[../architecture]], v1 is specced as the list-based #9 builder *(assumed)*; a drag-canvas needs an ADR + new ui-strategy row.

## Related

- [[../_module|Workflow Builder]] · [[trigger-registry|Trigger Registry]] · [[action-registry|Action Registry]] · [[run-history|Run History]]
- [[../architecture]] · [[../../../../architecture/patterns/custom-pages]]
