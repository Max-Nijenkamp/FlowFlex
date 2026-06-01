---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.data-views
status: planned
color: "#4ADE80"
---

# Cross-Domain Data Views

Pre-built combined views that join data across domains (e.g. revenue per employee, deals per marketing source) — the "BI" depth beyond single-domain dashboards.

## Core Features

- Pre-built cross-domain views maintained by FlowFlex (e.g. CRM + Finance, HR + Projects)
- Examples: revenue per sales rep, project profitability (time cost vs invoiced), marketing source → closed revenue
- Read-only aggregated query views (never expose raw cross-tenant data)
- Drill-down: click an aggregate to see underlying records
- Each view respects CompanyScope and module activation (only shows views for active domains)
- Export view data

## Data Model

No persistent tables — these are query-time aggregations across domain tables, always filtered by `company_id`. Heavy queries cached (see [[architecture/caching]]).

## Filament

**Nav group:** Data Views

- `DataViewsPage` (custom page) — gallery of available cross-domain views (filtered to active modules)
- Each view is a custom page with charts + drill-down table

## Cross-Domain / Performance

- Aggregates across multiple domain tables — must use indexed queries, caching (see [[architecture/performance]])
- View availability depends on which domains the company has activated

## Related

- [[domains/analytics/dashboards]]
- [[architecture/performance]]
- [[architecture/caching]]
