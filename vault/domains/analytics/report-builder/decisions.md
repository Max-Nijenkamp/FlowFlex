---
domain: analytics
module: report-builder
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Builder — Decisions

---

## ADR: Whitelisted source registry, never arbitrary tables/columns

Reports are composed only over columns a domain explicitly registers in `ReportSourceRegistry` as `whitelisted_columns`. Encrypted/sensitive fields are never whitelisted, so no-code reporting can never surface a salary, national ID, or IBAN. This is the report builder's core security stance and mirrors the `MetricRegistry` boundary of [[../dashboards/_module|dashboards]] ([[../../../security/data-ownership]]).

---

## ADR: Eloquent query composition, never raw SQL

`ReportRunner` builds an Eloquent query under `CompanyScope`; it never accepts or composes raw SQL. This guarantees tenant scoping is always applied and removes SQL-injection surface. `ReportIsolationTest` (no cross-company rows) is the domain's most important test.

---

## ADR: Reports are single-source; cross-domain joins live in Data Views

The report builder targets one whitelisted entity per report (with its own columns). Cross-domain joins (revenue-per-rep etc.) are curated, code-shipped [[../data-views/_module|Data Views]], not user-composed — keeping the high-risk join surface controlled.

---

## Implementation Notes

- Preview capped at 100 rows; export queued + chunked via `ExportReportJob` on the `exports` queue.
- No result cache in v1 *(assumed)* — reports are on-demand.
- A saved `bi_reports` row is a schedulable source for [[../scheduled-exports/_module|analytics.exports]].
