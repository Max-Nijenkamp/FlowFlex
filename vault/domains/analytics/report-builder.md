---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.reports
status: planned
color: "#4ADE80"
---

# Report Builder

Build custom tabular reports: select a data source, choose columns, apply filters and grouping, and export. No-code reporting across domains.

## Core Features

- Report definition: data source (domain entity), columns, filters, grouping, sorting
- Aggregations: count, sum, average, min, max per grouped column
- Filters: field-based conditions with AND/OR
- Preview report results in-panel
- Save report definitions for reuse
- Export to Excel/CSV (`maatwebsite/laravel-excel`)
- Tenant-scoped: only the company's own data (CompanyScope enforced)
- Run on demand or save as a scheduled export (see Scheduled Exports)

## Data Model

| Table | Key Columns |
|---|---|
| `bi_reports` | company_id, name, data_source, columns (json), filters (json), grouping (json), sorting (json), owner_id |

## Filament

**Nav group:** Reports

- `ReportBuilderPage` (custom page) — source selector, column picker, filter builder, live preview
- `ReportResource` — list saved reports, run, export

## Cross-Domain / Security

- Always enforces CompanyScope — no raw cross-tenant queries (see [[architecture/multi-tenancy]])

## Related

- [[domains/analytics/dashboards]]
- [[domains/analytics/scheduled-exports]]
