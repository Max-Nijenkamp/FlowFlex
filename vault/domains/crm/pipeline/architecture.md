---
domain: crm
module: pipeline
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Pipeline — Architecture

See also [[_module|pipeline._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/custom-pages]], [[../../../architecture/websockets]], [[../../../architecture/ui-strategy]].

---

## Services & Actions

- `PipelineService::board(BoardFilterData $filters): BoardData` — one query for deals + stages, grouped; weighted totals via brick/money
- `ReorderStagesAction::run(array $orderedIds): void`
- Stage moves delegated to: `DealService::moveToStage` (deals module owns the rule: closed deals immutable)

---

## Events (broadcast-only, not domain events)

`DealStageChanged` — `ShouldBroadcast` on `company.{id}.crm` private channel.

Payload:
```json
{
  "deal_id": "<ulid>",
  "from_stage_id": "<ulid>",
  "to_stage_id": "<ulid>",
  "moved_by": "<user_id>"
}
```

Not in the domain event map — UI sync only. See [[../../../architecture/websockets]] and [[../../../infrastructure/websockets-reverb]].

---

## Filament Artifacts

**Nav group:** Pipeline

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `PipelineBoardPage` | #3 Kanban custom page | Livewire + Alpine sortable; Reverb broadcast (collaborative); quick-add; filters in header |
| `PipelineStageResource` | #1 CRUD resource | stage config, reorder |

Pattern reference: [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].

---

## Search & Realtime

Realtime: Reverb broadcast on `company.{id}.crm` (ui-strategy row #3 — collaborative board). Echo listener re-renders moved card only. See [[../../../infrastructure/websockets-reverb]].

No Meilisearch index planned for this module — filter state is in-memory on the board.

---

## Jobs & Scheduling

None. All pipeline operations are synchronous; stage reorder is a direct action.
