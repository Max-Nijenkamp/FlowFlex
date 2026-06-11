---
type: module
domain: Analytics & BI
domain-key: analytics
panel: analytics
module-key: analytics.reports
status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [analytics.exports]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [bi_reports]
permission-prefix: analytics.reports
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Report Builder

Build custom tabular reports: select a data source, choose columns, apply filters and grouping, and export. No-code reporting across domains.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/analytics/scheduled-exports\|analytics.exports]] | saved reports schedulable |

---

## Core Features

- Report definition: data source (whitelisted domain entity), columns, filters, grouping, sorting
- **Source registry**: domains register reportable entities with whitelisted columns — encrypted/sensitive columns NEVER reportable; sources of inactive modules hidden
- Aggregations: count, sum, average, min, max per grouped column
- Filters: field-based conditions with AND/OR
- Preview report results in-panel (limit 100 rows)
- Save report definitions for reuse
- Export to Excel/CSV (`maatwebsite/laravel-excel`, queued for large sets per [[architecture/queue-jobs]])
- Tenant-scoped: CompanyScope inherent — builder composes Eloquent queries, never raw SQL

---

## Data Model

### bi_reports — id, company_id (indexed), name, data_source (registry key), columns/filters/grouping/sorting (jsonb, registry-validated), owner_id FK, deleted_at

---

## DTOs

### CreateReportData — name, data_source (registered + module active), columns[] (in source whitelist), filters (operators in set), grouping/sorting (whitelisted columns)

## Services & Actions

- `ReportSourceRegistry::register(string $key, SourceDefinition $def)` — entity, whitelisted columns, filterable fields
- `ReportRunner::run(Report $r, ?int $limit): Collection` — composes query under CompanyScope; aggregations via SQL
- `ExportReportJob` — `exports` queue, chunked

---

## Filament

**Nav group:** Reports

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ReportBuilderPage` | #9 report builder custom page | source/column/filter pickers, live preview |
| `ReportResource` | #1 CRUD resource | saved reports, run + export actions |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('analytics.reports.view-any') && BillingService::hasModule('analytics.reports')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a rate limiter on the analytics.reports.run/export actions per architecture/security.md.

---

## Permissions

`analytics.reports.view-any` · `analytics.reports.create` · `analytics.reports.run` · `analytics.reports.export`

---

## Test Checklist

- [ ] Tenant isolation: report never returns other-company rows (the critical test)
- [ ] Module gating; inactive-module sources hidden
- [ ] Non-whitelisted column rejected (incl. encrypted fields)
- [ ] Aggregations + AND/OR filters correct over fixtures
- [ ] Preview capped at 100 rows; export chunked
- [ ] Saved report re-runs identically

---

## Build Manifest

```
database/migrations/xxxx_create_bi_reports_table.php
app/Models/Analytics/Report.php
app/Data/Analytics/CreateReportData.php
app/Support/Analytics/{ReportSourceRegistry,SourceDefinition,ReportRunner}.php
app/Jobs/Analytics/ExportReportJob.php
app/Filament/Analytics/Pages/ReportBuilderPage.php
app/Filament/Analytics/Resources/ReportResource.php
database/factories/Analytics/ReportFactory.php
tests/Feature/Analytics/{ReportBuilderTest,ReportIsolationTest}.php
```

---

## Related

- [[domains/analytics/dashboards]]
- [[domains/analytics/scheduled-exports]]
- [[architecture/multi-tenancy]]
