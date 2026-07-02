---
domain: analytics
module: report-builder
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Builder — Architecture

## The ReportSourceRegistry (the whitelist boundary)

Like [[../dashboards/features/metric-registry|MetricRegistry]] for dashboards, the report builder never queries arbitrary tables. Each reporting-enabled domain registers a **source definition**:

- `ReportSourceRegistry::register(string $key, SourceDefinition $def)` — `$def` names the entity/model, its **whitelisted columns**, filterable fields, and groupable/aggregatable columns. Encrypted or sensitive columns are **never** whitelisted, so they cannot appear in a report.
- `available(): Collection` — sources filtered by `BillingService::hasModule(...)`; inactive-module sources disappear.

`ReportRunner::run(Report $r, ?int $limit): Collection` composes an **Eloquent** query (never raw SQL) over the whitelisted source under `CompanyScope`, applies filters (operator-validated), grouping + SQL aggregations, and sorting — capped for preview, chunked for export.

Because the runner only touches columns the owning domain whitelisted, and always under CompanyScope, the data-ownership + sensitivity boundary ([[../../../security/data-ownership]]) holds by construction.

---

## Services & Actions

- `ReportSourceRegistry` / `SourceDefinition` (`app/Support/Analytics/`) — registration + module-filtered listing
- `ReportRunner::run(Report, ?limit): Collection` — CompanyScope-safe query composition + aggregation
- `ExportReportJob` — queued (`exports` queue), chunked Excel/CSV generation
- No Interface→Service split for v1 *(assumed)*

---

## Events

None fired, none consumed. Reports resolve on demand; export is a queued job, not an event.

---

## Filament Artifacts

**Nav group:** Reports

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ReportBuilderPage` | custom-page (report builder) | source/column/filter/grouping pickers + live preview |
| `ReportResource` | simple-resource | saved reports, run + export row actions |

**Access contract:** `canAccess() = Auth::user()->can('analytics.reports.view-any') && BillingService::hasModule('analytics.reports')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly.

---

## Caching

No persistent report-result cache in v1 *(assumed)* — reports are on-demand; preview is capped at 100 rows and export is chunked. Heavy repeated reports lean on the source tables' own indexes ([[../../../architecture/performance]]).

---

## Search & Realtime

- Search: none.
- Realtime: none — preview recomputes on builder change; export is async via queue.

---

## Security Notes

See [[./security]]. Two domain-defining controls: (1) **column whitelist** — only registry-whitelisted, non-sensitive columns are selectable; (2) **Eloquent-only, CompanyScope-bound** query composition — no raw SQL, no cross-company rows. Run/export actions are rate-limited ([[../../../architecture/security]]).
