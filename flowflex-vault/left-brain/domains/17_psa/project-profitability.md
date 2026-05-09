---
type: module
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
status: planned
migration_range: 876000–877999
last_updated: 2026-05-09
---

# Project Profitability

Revenue vs cost analysis per project, per client, per engagement type. Identifies margin by project, spots unprofitable engagements before they drain the business.

---

## Core Metrics

### Project P&L
```
Revenue
  - Invoiced to date
  - WIP (work in progress, not yet invoiced)
  - Remaining contracted value

Costs
  - Internal staff costs (hours × fully-loaded rate)
  - Subcontractor costs
  - Out-of-pocket expenses
  - Software/tooling costs (allocated)

Gross Margin = Revenue - Direct Costs
Gross Margin % = Gross Margin / Revenue × 100
```

### Fully-Loaded Rate
Internal staff cost = actual salary + employer NI/contributions + overhead allocation (office, tools, management overhead), expressed as hourly rate.

Each employee has a `cost_rate` (confidential) separate from their `sell_rate` (what clients are charged). The spread is the contribution per billable hour.

### At-Completion Forecast
- Estimate remaining hours to complete (from PM estimate in Projects)
- Extrapolate cost and revenue to project end
- Flag: if forecast margin < threshold (configurable, default 15%) → warning

---

## Views

### Project Drill-Down
- Revenue recognition breakdown (invoiced, WIP, remaining)
- Cost breakdown by person (hours × cost rate) — confidential, visible only to finance/directors
- Expenses by category
- Actual margin vs contracted margin at quote time
- Change order impact: if SOW changed, show original vs revised margin

### Client P&L
Roll up all engagements for a client:
- Total revenue invoiced
- Total cost
- Client lifetime margin %
- Trend: improving/declining client profitability over 12 months

### Engagement Type P&L
- Retainer engagements: average margin %
- Fixed price: average margin % (reveals underquoting patterns)
- T&M: average margin % (typically highest but variable)

---

## Data Model

### `psa_project_profitability_snapshots`
Weekly computed per project:

| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| engagement_id | ulid | FK `psa_engagements` |
| project_id | ulid | FK `projects` |
| snapshot_week | date | Monday |
| revenue_invoiced | decimal(14,2) | |
| revenue_wip | decimal(14,2) | |
| cost_staff | decimal(14,2) | hours × cost_rate |
| cost_subcontractor | decimal(14,2) | |
| cost_expenses | decimal(14,2) | |
| gross_margin | decimal(14,2) | computed |
| gross_margin_pct | decimal(5,2) | computed |
| forecast_margin_pct | decimal(5,2) | at-completion forecast |

---

## Access Control

Staff cost rates are confidential. Two tiers:
- **Project Manager**: sees revenue, hours, margin % — NOT absolute cost figures
- **Director / Finance**: sees full P&L including cost rates

---

## Integrations

- **Finance** — invoiced revenue from AR; expense records from expenses module
- **Projects** — time entries (hours) + PM estimates (remaining effort)
- **HR** — employee cost rates (from payroll/compensation data)

---

## Migration

```
876000_create_psa_project_profitability_snapshots_table
876001_create_psa_employee_cost_rates_table
876002_create_psa_sell_rates_table
```

---

## Related

- [[MOC_PSA]]
- [[client-engagement-management]]
- [[utilisation-capacity-tracking]]
- [[agency-billing-intelligence]]
- [[MOC_Finance]] — revenue and cost source
