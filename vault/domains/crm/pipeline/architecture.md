---
domain: crm
module: pipeline
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `PipelineBoardPage` | #3 Kanban custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] | Livewire + Alpine sortable; Reverb broadcast (collaborative, `company.{id}.crm`); quick-add; header filters; **drag delegates the deal stage move to [[../deals/_module\|crm.deals]] `DealService::moveToStage`** |
| `PipelineStageResource` | #1 CRUD resource | standard resource; `ReorderStagesAction` for column order | stage config, reorder; stage with deals cannot be deleted |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.pipeline.view') && BillingService::hasModule('crm.pipeline')`
per [[../../../architecture/filament-patterns]] #1. `PipelineBoardPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. The board owns no deal write: the stage move is delegated
to `crm.deals` `DealService::moveToStage`, which enforces `crm.deals.update` and the closed-deal-immutable rule.
The Reverb channel `company.{id}.crm` is a private channel authorised to same-company users
([[../../../architecture/websockets]]).

---

## Search & Realtime

Realtime: Reverb broadcast on `company.{id}.crm` (ui-strategy row #3 — collaborative board). Echo listener re-renders moved card only. See [[../../../infrastructure/websockets-reverb]].

No Meilisearch index planned for this module — filter state is in-memory on the board.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Pipeline / stage CRUD (`PipelineStageResource` form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Stage reorder (`ReorderStagesAction`) | Optimistic | `updated_at` stale-check on the stage set; concurrent reorder surfaces the conflict rather than last-write-wins ([[../../../architecture/patterns/optimistic-locking]]) |
| Board drag → deal stage move | n-a (delegated) | the board owns no write — the stage transition is a Pessimistic move owned by [[../deals/_module\|crm.deals]] (`DealService::moveToStage`, `DB::transaction()` + `lockForUpdate()` per [[../../../architecture/patterns/states]]); the board delegates and re-renders from the server on exception |
| `DealStageChanged` broadcast | n-a | broadcast-only transport, no persistence — the move already committed in crm.deals |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None. All pipeline operations are synchronous; stage reorder is a direct action.
