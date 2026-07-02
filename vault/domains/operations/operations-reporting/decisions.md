---
domain: operations
module: operations-reporting
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Operations Reporting — Decisions & ADR Notes

## Owns No Tables — Pure Read Aggregation

**Context:** A reporting module could denormalise metrics into its own tables for speed.

**Decision:** v1 owns no tables. `OperationsAnalyticsService` reads across the other Operations modules and caches results in Redis (TTL-based). No stored aggregates.

**Consequences:** Zero write surface (nothing to keep in sync, trivial data-ownership story). Cost: heavier queries, mitigated by caching (1 h historical / 15 min current). If it ever outgrows this, a denormalised projection Operations owns + refreshes from events is the escalation — a future ADR.

---

## TTL Cache, Not Event-Invalidated

**Decision:** The metrics cache is invalidated by TTL only, not by stock/receipt events.

**Consequences:** Simpler; up to 15 min staleness on the current window is acceptable for a reporting dashboard. Event-based invalidation is a later optimisation.

---

## Soft-Dep Sections Degrade, Not Break

**Decision:** Purchasing-spend and supplier-performance sections are hidden when their modules (PO / suppliers) are inactive, rather than erroring.

**Consequences:** The dashboard works with inventory alone and grows richer as more Operations modules are activated.
