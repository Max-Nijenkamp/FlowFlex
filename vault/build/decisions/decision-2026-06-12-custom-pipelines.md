---
type: adr
date: 2026-06-12
status: decided
domain: CRM
color: "#F97316"
---

# Custom pipelines (Pipedrive pattern)

## Context

crm.pipeline shipped with a single implicit pipeline (stages directly on the company). Founder wants multiple pipelines with user-managed stages, switchable on the board, like Pipedrive.

## Decision

- New `crm_pipelines` table (name, is_default, order); `crm_pipeline_stages.pipeline_id` FK added, backfilled into a default "Sales pipeline" per company in the migration
- `PipelineResource` (Sales → Pipelines): pipeline CRUD + Stages relation manager (order, default probability); stages with deals cannot be deleted, pipelines with dealed stages cannot be deleted
- Board: pill switcher across pipelines, per-stage value subtotals, open-value header, "New deal" header action creating into any stage of the active pipeline
- Deal form: stage select grouped by pipeline

## Consequences

- Deals keep `stage_id` only — pipeline is derived via the stage (no second FK to drift)
- DealService.moveToStage untouched; cross-pipeline moves possible by design
- crm.pipeline spec needs a v2 refresh to document the pipelines table (follow-up)
