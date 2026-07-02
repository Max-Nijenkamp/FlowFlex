---
domain: analytics
module: data-views
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cross-Domain Data Views

Pre-built combined views that join data across domains (e.g. revenue per employee, deals per marketing source) — the "BI depth" beyond single-domain dashboards. Owns **no tables**; every view is a shipped `DataView` class that reads each source domain through that domain's own read path.

> Planned for build. Any "shipped/built" language reflects the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

---

## Module-key

`analytics.data-views`

**Priority:** p3
**Panel:** analytics
**Permission prefix:** `analytics.data-views`
**Tables:** — (none; query-time aggregations only)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../dashboards/_module\|analytics.dashboards]] | reuses the `MetricRegistry` / read-consumer pattern |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | source modules per view (CRM, Finance, HR, Projects, Marketing) | a view is only offered when all its `requiredModules()` are active |

---

## Core Features

- Pre-built cross-domain views maintained by FlowFlex, shipped as registered `DataView` classes
- v1 set *(assumed)*: revenue-per-rep (CRM+Finance), project-profitability (Projects+Finance), marketing-source→revenue (Marketing+CRM+Finance), revenue-per-employee (HR+Finance)
- Read-only aggregated query results — every source read is CompanyScope-safe
- Drill-down: click an aggregate to see the underlying records
- Each view declares `requiredModules()` — only fully-active views are listed
- Export view data (Excel)

See feature notes: [[./features/view-registry|View Registry]] · [[./features/view-explorer|View Explorer]] · [[./features/drill-down|Drill-Down]] · [[./features/view-export|View Export]].

---

## Build Manifest

```
app/Support/Analytics/{DataViewRegistry,DataViewContract,DataViewResult}.php
app/Analytics/Views/{RevenuePerRepView,ProjectProfitabilityView,MarketingSourceRevenueView,RevenuePerEmployeeView}.php
app/Filament/Analytics/Pages/DataViewsPage.php
tests/Feature/Analytics/DataViewsTest.php
```

---

## Test Checklist

- [ ] Tenant isolation: every shipped view returns only current-company aggregates (per-view test)
- [ ] Module gating: page hidden when `analytics.data-views` inactive; view hidden when any `requiredModules()` entry is inactive
- [ ] Drill-down rows reconcile with the aggregate
- [ ] Revenue-per-rep + project-profitability math over fixtures
- [ ] Indexed queries (no full scans on fixtures — explain check *(assumed: manual)*)
- [ ] Export throttled + tenant-scoped file

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | each source domain's query/read API inside the `DataView::run()` closure | CRM, Finance, HR, Projects, Marketing | Every read runs under the owning domain's `CompanyContext`; Analytics composes/aggregates but never writes. No events. |

**Data ownership:** `analytics.data-views` owns **no tables** and writes nothing. Each view reads its source domains through their own read paths (registered query closures), never their tables directly ([[../../../security/data-ownership]]).

---

## Related

- [[../dashboards/_module|analytics.dashboards]]
- [[../report-builder/_module|analytics.reports]]
- [[../../../architecture/performance]] · [[../../../architecture/caching]]
- [[../../../security/data-ownership]]
