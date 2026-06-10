---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.pipeline
status: planned
priority: v1-core
depends-on: [crm.deals, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages, websockets]
tables: [crm_pipeline_stages]
permission-prefix: crm.pipeline
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Pipeline Board

Visual Kanban board with deal cards grouped by stage. Drag-and-drop stage changes. The primary way sales reps manage their pipeline. Owns `crm_pipeline_stages`; the board reads `crm_deals`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] | the cards; stage moves call `DealService::moveToStage` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

(Circular note: crm.deals hard-depends on crm.pipeline for the stages table — build order: pipeline stages migration ships first, board page after deals. BUILD-ORDER sequence contacts → deals → pipeline handles this: stage table migration belongs to THIS module but is created in the deals build step as its FK target *(assumed: single migration, owned here)*.)

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

## Data Model

### crm_pipeline_stages

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| name | string | not null | unique `(company_id, name)` |
| order | int | not null | board column order |
| probability_default | decimal(5,2) | not null | applied on stage entry |
| is_won / is_lost | boolean | default false | exactly one of each per company *(assumed)* |
| deleted_at | timestamp | nullable | stage with deals cannot be deleted — reassign first |

Default stages seeded on module activation: Lead → Qualified → Proposal → Won / Lost *(assumed)*.

---

## DTOs

### CreateStageData — name (required, unique per company), order, probability_default (0–100), is_won/is_lost
### MoveDealData — deal_id, stage_id (both in company) — delegates to DealService

## Services & Actions

- `PipelineService::board(BoardFilterData $filters): BoardData` — one query for deals + stages, grouped; weighted totals via brick/money
- `ReorderStagesAction::run(array $orderedIds): void`
- Stage moves: `DealService::moveToStage` (deals module owns the rule: closed deals immutable)

## Events (broadcast-only, not domain events)

`DealStageChanged` — `ShouldBroadcast` on `company.{id}.crm` private channel (`deal_id`, `from_stage_id`, `to_stage_id`, `moved_by`). Not in the domain event map — UI sync only ([[architecture/websockets]]).

---

## Filament

**Nav group:** Pipeline

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `PipelineBoardPage` | #3 Kanban custom page | Livewire + Alpine sortable; Reverb broadcast (collaborative); quick-add; filters in header |
| `PipelineStageResource` | #1 CRUD resource | stage config, reorder |

---

## Permissions

`crm.pipeline.view` · `crm.pipeline.move-deals` · `crm.pipeline.manage-stages`

---

## Search & Realtime

Realtime: Reverb broadcast on `company.{id}.crm` (ui-strategy row #3 — collaborative board). Echo listener re-renders moved card only.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Board groups deals per stage with correct weighted totals (one query — no N+1)
- [ ] Drag move calls DealService (closed-deal move rejected through the same path)
- [ ] `DealStageChanged` broadcast on company channel with correct payload
- [ ] Stage with deals cannot be deleted
- [ ] Filters (owner/value range) restrict cards correctly
- [ ] Default stages seeded on activation; exactly one won + one lost stage

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

## Related

- [[domains/crm/deals]]
- [[architecture/patterns/custom-pages]]
- [[architecture/websockets]]
- [[architecture/ui-strategy]]
