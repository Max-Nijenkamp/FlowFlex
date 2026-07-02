---
domain: analytics
module: report-builder
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ReportBuilderPage` | #9 Report builder custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] | source/column/filter/grouping pickers + live preview; Export action names the `exports` limiter; satisfies [[../../../architecture/patterns/custom-page-checklist]] |
| `ReportResource` | #1 CRUD resource | tweaks: `custom-header-actions` (run, export) | saved reports; run + export row actions each carry their own permission |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('analytics.reports.view-any') && BillingService::hasModule('analytics.reports')`
per [[../../../architecture/filament-patterns]] #1. `ReportBuilderPage` is a custom page — Filament does not auto-gate custom pages, so it MUST declare `canAccess()` explicitly. Public/portal surfaces would declare a guest or scoped-portal guard instead (Vue+Inertia per [[../../../architecture/ui-strategy]]); Analytics has none.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Report definition CRUD (source, columns, filters, grouping) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` conflict notification with Reload action ([[../../../architecture/patterns/optimistic-locking]]) |
| Report run / preview (`ReportRunner::run`) | n/a | Read-only — composes a CompanyScope Eloquent query, writes nothing |
| Report export (`ExportReportJob`) | n/a | Read-only — chunked read of whitelisted source columns; produces a file, mutates no domain table |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]. Only the saved `bi_reports` definition is editable; runs and exports read source data and never mutate it.

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
