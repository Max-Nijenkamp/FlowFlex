---
domain: analytics
module: data-views
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cross-Domain Data Views — Unknowns & Assumptions

All items are unverified — authoritative defaults at build time, overridable via ADR.

---

## Open Questions

1. **v1 view set.** Revenue-per-rep, project-profitability, marketing-source→revenue, revenue-per-employee are *(assumed)*. Confirm the shipped list and each view's exact columns.
2. **Drill-down depth.** One level (aggregate → records) *(assumed)*. Multi-level drill (rep → deals → line items) unconfirmed.
3. **Cache TTL.** 1 h flat *(assumed)*. Some views (finance) may need shorter, some (HR headcount) longer.
4. **Aggregation locus.** In-memory join over per-domain reads vs a CompanyScope-safe cross-table query — per view, unconfirmed; affects index strategy.
5. **Export format.** Excel only *(assumed)*; PDF snapshot may be wanted (would lean on `analytics.exports`).

---

## Assumed Items (unverified)

- `*(assumed)*` — the four v1 views listed above.
- `*(assumed)*` — 1 h cache TTL per `(view, range)`.
- `*(assumed)*` — single-level drill-down.
- `*(assumed)*` — Excel-only export, throttled, tenant-scoped.
- `*(assumed)*` — plain classes + singleton registry, no Interface→Service split for v1.

> [!warning] UNVERIFIED
> No codebase exists (stripped to app/admin shell — [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Every view contract and cache figure is spec-derived, not code-verified.
