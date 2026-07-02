---
domain: crm
module: pipeline
feature: kanban-board
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban Board

Visual pipeline board — one column per stage, deal cards dragged between stages.

- Custom Filament page ([[../../../../architecture/patterns/custom-pages]]); one column per
  `crm_pipeline_stage` (custom per company: name, order, default probability, won/lost flags).
- Deal cards: name, value, account, owner, days-in-stage, probability.
- Drag-and-drop → `DealService::moveToStage` → broadcasts `DealStageChanged` ([[realtime-sync]]).

## UI

- **Kind**: custom-page — Kanban board with drag between stage columns ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `PipelineBoardPage` at `/crm/pipeline` (custom Filament page + Livewire `PipelineBoard`).
- **Layout**: one column per `crm_pipeline_stage` (name, order, probability, won/lost flags); deal cards show name, value, account, owner, days-in-stage, probability; board header shows total pipeline value + count per stage; zero-deal stages collapsible.
- **Key interactions**: drag card between columns → `DealService::moveToStage` → broadcast `DealStageChanged`; quick-add deal from column header; filter by owner/account/date/value/tag.
- **States**: empty (no stages → prompt to seed defaults; no deals → empty columns) · loading (skeleton cards) · error (closed-deal move rejected → card snaps back) · selected (dragged card highlighted, drop target outlined).
- **Gating**: view `crm.pipeline.view`; move calls `crm.deals.update` through `DealService`.

## Data

- Owns / writes: `crm_pipeline_stages` (+ pipelines) — stage config only.
- Reads: `crm_deals` (cards; grouped by stage in one query, no N+1).
- Cross-domain writes: none — a stage move mutates `crm_deals` via `DealService`, the owning service, not by direct write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `DealStageChanged` (ShouldBroadcast) → consumed by other viewers' boards ([[realtime-sync]]).
- Shared entity: `crm_deals` owned by crm.deals; the board is a read+command view over them.

## Related

- [[../_module|Pipeline]] · [[realtime-sync]] · [[../../deals/_module]] · [[../../../../decisions/decision-2026-06-12-custom-pipelines]]
