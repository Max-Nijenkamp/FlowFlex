---
domain: workplace
module: workplace-analytics
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace Analytics — Decisions

> Reconstructed from the flat source spec. Ratify during the v2 rebuild.

## ADR: Owns no tables — pure read-only aggregator

- **Context:** Analytics needs data from rooms, desks, visitors, maintenance.
- **Decision:** Own no tables; compute metrics on demand from the other modules' read models, cached per company + date range. Never write another domain's tables.
- **Consequences:** Canonical query-side example of [[../../../security/data-ownership]]; no write path → no cross-context escalation risk. Trade-off: compute cost mitigated by caching.

## ADR: Soft dependencies hide sections when inactive

- **Decision:** Desk / visitor / maintenance sections render only when their module is active for the company; rooms is the hard anchor.
- **Consequences:** The dashboard degrades gracefully as modules are toggled; no errors on missing data.

## ADR: Cache with split TTL (historical vs current)

- **Decision:** 1 h TTL for historical ranges, 15 min for ranges including today; TTL-only invalidation.
- **Consequences:** Cheap, fresh-enough metrics; no event-driven cache busting needed for v1.
