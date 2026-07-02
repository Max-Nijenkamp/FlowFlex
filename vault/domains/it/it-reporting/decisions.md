---
domain: it
module: it-reporting
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Reporting — Decisions

---

## Owns No Tables — Pure Read / Aggregate

`it.reporting` deliberately owns **zero tables**. It is a reporting layer, not a data owner: every metric is aggregated live from the five IT source tables (`it_assets`, `it_licences`, `it_tickets`, `it_mdm_devices`, `it_access_grants`) via their owning modules' read APIs. It never writes — not its own tables (it has none) and never another domain's ([[../../../security/data-ownership]]). This keeps the audit trail and write-ownership for each table unambiguous and gives the module zero blast radius.

---

## Soft-Dep Sections Hidden When Module Inactive

Only `it.assets` is a hard dependency. `it.licences`, `it.helpdesk`, `it.mdm`, and `it.access` are soft. When a soft-dep module is not active for the company, `ItAnalyticsService::metrics` returns `null` for that section (checked via `BillingService::hasModule(...)` before the query runs) and the corresponding widget does not render — **no error, no empty scaffolding, no query against a possibly-missing table**. The dashboard degrades gracefully to whatever IT modules the company actually runs.

---

## Caching: TTL-Only Invalidation

Aggregates are cached in Redis under `company:{id}:it:metrics:{from}:{to}`. Dashboard staleness is acceptable, so invalidation is **TTL-only** — 1 h for historical ranges, 15 min for the current period. There is no event-driven cache busting: it.reporting consumes no events, and reporting numbers do not need to be real-time. This keeps the caching layer simple and avoids coupling to sibling modules' write events.

---

## brick/money for Spend and Waste

All monetary figures — asset inventory value, licence monthly/annual spend, and waste (unused-seat cost) — are computed with `brick/money` on integer minor units, never raw float arithmetic. This guarantees rounding consistency across the spend and waste aggregates that feed the dashboard. See [[../../../architecture/packages]] (brick/money section).
