---
domain: crm
module: pipeline
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-05
---

# Pipeline Board

Visual Kanban board with deal cards grouped by stage. Drag-and-drop stage changes. The primary way sales reps manage their pipeline. Owns `crm_pipeline_stages`; the board reads `crm_deals`.

> All work here is **planned** — the CRM code was stripped back to an app/admin shell. See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

---

## Module-key

`crm.pipeline`

**Priority:** v1-core *(assumed — hard dependency of the v1-core `crm.deals` module)*  
**Panel:** crm  
**Permission prefix:** `crm.pipeline`  
**Tables:** `crm_pipelines`, `crm_pipeline_stages`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../deals/_module\|crm.deals]] | the cards; stage moves call `DealService::moveToStage` |
| Hard | core.billing + core.rbac | gating + permissions |

**Circular note:** `crm.deals` hard-depends on `crm.pipeline` for the stages table — build order: pipeline stages migration ships first, board page after deals. BUILD-ORDER sequence contacts → deals → pipeline handles this: the stage table migration belongs to this module but is created in the deals build step as its FK target *(assumed: single migration, owned here)*.

---

## Core Features

- Kanban board with one column per pipeline stage
- Custom pipeline stages per company: name, order, default probability, won/lost flags
- Deal cards: name, value, account, owner, days in stage, probability
- Drag-and-drop deal to new stage (calls `DealService::moveToStage`, broadcasts `DealStageChanged`)
- Filter by: owner, account, date range, value range, tag
- Board-level metrics: total pipeline value, count per stage
- Collapsed stage view: collapse stages with zero deals
- Quick-add deal from column header
- Live updates: other viewers see card moves via Reverb (collaborative view)

---

## Build Manifest

```
database/migrations/xxxx_create_crm_pipeline_stages_table.php
app/Models/CRM/PipelineStage.php
app/Data/CRM/{CreateStageData,MoveDealData,BoardData,BoardFilterData}.php
app/Services/CRM/PipelineService.php
app/Actions/CRM/ReorderStagesAction.php
app/Events/CRM/DealStageChanged.php (ShouldBroadcast)
app/Filament/CRM/Pages/PipelineBoardPage.php
resources/views/filament/crm/pages/pipeline-board.blade.php
app/Livewire/CRM/PipelineBoard.php
app/Filament/CRM/Resources/PipelineStageResource.php
database/factories/CRM/PipelineStageFactory.php
tests/Feature/CRM/{PipelineBoardTest,StageManagementTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot view, move, or configure company B deals/stages
- [ ] Module gating: artifacts hidden when `crm.pipeline` inactive
- [ ] Board groups deals per stage with correct weighted totals (one query — no N+1)
- [ ] Drag move calls DealService (closed-deal move rejected through the same path)
- [ ] `DealStageChanged` broadcast on company channel with correct payload
- [ ] Stage with deals cannot be deleted
- [ ] Filters (owner/value range) restrict cards correctly
- [ ] Default stages seeded on activation; exactly one won + one lost stage

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `DealStageChanged` (ShouldBroadcast) | crm.deals viewers | Broadcast board moves on per-company Reverb channel |
| Fires | stage-config changes | crm.deals | Stage set (name/order/probability/won-lost flags) reference data |
| Reads | `crm_deals` | crm.deals | Cards on the board; grouped per stage (one query, no N+1) |
| Reads/Commands | `DealService::moveToStage` | crm.deals | Drag move mutates `crm_deals` via its owning service, not directly |

**Data ownership:** `crm.pipeline` writes only `crm_pipeline_stages` (+ pipelines); all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

---

## Related

- [[../deals/_module|crm.deals]]
- [[../activities/_module|crm.activities]]
- [[architecture|pipeline.architecture]]
- [[data-model|pipeline.data-model]]
- [[security|pipeline.security]]
- [[decisions|pipeline.decisions]]
- [[unknowns|pipeline.unknowns]]
- [[features/kanban-board|kanban-board feature]]
- [[features/realtime-sync|realtime-sync feature]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../architecture/websockets]]
- [[../../../architecture/ui-strategy]]
