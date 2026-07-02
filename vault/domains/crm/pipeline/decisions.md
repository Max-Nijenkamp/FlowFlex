---
domain: crm
module: pipeline
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Pipeline — Decisions

---

## ADR: Custom Pipelines per Company

[[../../../decisions/decision-2026-06-12-custom-pipelines]]

The `crm_pipelines` table (added 2026-06-12) introduces multi-pipeline support per company. Each company can have multiple named pipelines (e.g. "Sales", "Partnerships"), each with its own ordered stage set. Deals belong to a stage, which belongs to a pipeline.

**Migration note:** `pipeline_id` was added to `crm_pipeline_stages` on 2026-06-12 and backfilled to a default pipeline for existing companies.

---

## Build Order: Stages Migration Before Deals

The `crm_pipeline_stages` table migration must run before `crm_deals`, because deals carry a `stage_id` FK. The migration is owned by this module (pipeline) even though it's created in the deals build sequence to keep FK references clean.

---

## Stage Deletion: Reassign First

A stage with active deals cannot be deleted. The planned UI flow is to prompt the rep to reassign all deals to another stage before deletion is allowed.

---

## Won/Lost Stages: One of Each Per Company

Each pipeline is planned to enforce exactly one `is_won` and one `is_lost` stage. This ensures closed-deal state maps cleanly to board semantics. See [[unknowns|pipeline.unknowns]] for the unverified constraint on "exactly one per pipeline vs. per company".

---

## Implementation Notes

- `PipelineService::board` should issue a single query joining deals + stages + pipelines (no N+1)
- Weighted pipeline totals use brick/money for rounding consistency
- `DealService::moveToStage` (in the deals module) is the sole authority for rejecting moves to closed stages — pipeline board delegates, does not reimplement the rule
