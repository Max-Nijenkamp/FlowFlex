---
domain: analytics
module: report-builder
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Report Builder

No-code tabular reporting: pick a whitelisted data source, choose columns, apply filters + grouping, preview, save, and export. Reads source domains through a `ReportSourceRegistry` — never their tables directly.

> Planned for build. Any "shipped/built" language reflects the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

---

## Module-key

`analytics.reports`

**Priority:** p3
**Panel:** analytics
**Permission prefix:** `analytics.reports`
**Tables:** `bi_reports`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../scheduled-exports/_module\|analytics.exports]] | saved reports are schedulable export sources |

---

## Core Features

- Report definition: data source (whitelisted domain entity), columns, filters, grouping, sorting
- **Source registry**: domains register reportable entities with whitelisted columns — encrypted/sensitive columns NEVER reportable; inactive-module sources hidden
- Aggregations: count / sum / avg / min / max per grouped column
- Filters: field conditions with AND/OR
- In-panel preview (limit 100 rows)
- Save report definitions for reuse
- Export to Excel/CSV (`maatwebsite/laravel-excel`, queued for large sets)
- Tenant-scoped: builder composes Eloquent queries under CompanyScope, never raw SQL

See feature notes: [[./features/source-registry|Source Registry]] · [[./features/report-composer|Report Composer]] · [[./features/report-runner|Report Runner]] · [[./features/saved-reports|Saved Reports]].

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

## Test Checklist

- [ ] Tenant isolation: report never returns other-company rows (`ReportIsolationTest` — the critical test)
- [ ] Module gating: builder + resource hidden when `analytics.reports` inactive; inactive-module sources hidden
- [ ] Non-whitelisted column rejected (incl. encrypted fields)
- [ ] Aggregations + AND/OR filters correct over fixtures
- [ ] Preview capped at 100 rows; export chunked + throttled via the `exports` limiter
- [ ] Saved report re-runs identically
- [ ] Stale-write: concurrent edit of a saved report definition surfaces the conflict notification (optimistic)

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `ReportSourceRegistry` source definitions (entity + whitelisted columns) | every reporting-enabled domain | The runner composes a CompanyScope-safe Eloquent query over the registered source; only whitelisted columns are selectable — encrypted/sensitive fields never reportable. No events. |
| Feeds | saved report reference | [[../scheduled-exports/_module\|analytics.exports]] | a saved `bi_reports` row is a schedulable export source (exports reads it) |

**Data ownership:** `analytics.reports` writes only `bi_reports`. Source data is read through registry-whitelisted, CompanyScope-safe queries — Analytics never writes another domain's tables, and the column whitelist blocks reading sensitive fields ([[../../../security/data-ownership]]).

---

## Related

- [[../dashboards/_module|analytics.dashboards]]
- [[../scheduled-exports/_module|analytics.exports]]
- [[../../../architecture/multi-tenancy]] · [[../../../security/data-ownership]]
