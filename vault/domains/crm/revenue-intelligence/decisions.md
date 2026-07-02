---
domain: crm
module: revenue-intelligence
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Revenue Intelligence — Decisions

## ADR: Deterministic rules, not ML, for v1

**Status**: decided (from spec). v1 deal health scoring is deterministic weighted rules *(assumed)*, not machine learning. LLM-based win/loss summaries via [[../../ai/copilot/_module|AI Copilot]] are P3.

**Consequences**: scores are explainable and testable (deterministic over fixtures); no model training or inference dependency for v1.

## ADR: Win/loss rows via direct service call (same-domain rule)

**Status**: decided (from spec). Win/loss rows are created on deal close by a direct call from `DealService`, not via cross-domain events, because both live in the CRM domain.

**Consequences**: keeps intra-domain coupling direct and synchronous per the event-bus rule that events are for cross-domain triggers only. See [[../../../architecture/event-bus]].

## ADR: Default factor weights 30/30/20/20

**Status**: decided (assumed). Health score factors default to activity recency 30, stage velocity 30, engagement 20, deal age 20 *(assumed)*; weights are configurable.

**Consequences**: predictable baseline scoring; weights can be tuned per company without code changes.
