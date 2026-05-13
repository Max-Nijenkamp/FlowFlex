---
type: module
domain: Professional Services (PSA)
panel: psa
module-key: psa.profitability
status: planned
color: "#4ADE80"
---

# Profitability

> Read-only project profitability analysis — budget vs actuals, gross margin tracking, and cost allocation by project and phase.

**Panel:** `psa`
**Module key:** `psa.profitability`

---

## What It Does

Profitability gives firm leaders and project managers a clear financial picture of each client engagement. It pulls together the contract value from Project Delivery, actual time and expenses from Time & Billing, and resource costs from Resource Planning to calculate gross margin, cost overrun, and profitability versus plan. All views are read-only aggregations — there is no data entry here. Drill-down by phase, deliverable, or team member isolates where profit is being made or eroded.

---

## Features

### Core
- Project P&L view: contract value, total cost incurred, gross profit, and gross margin percentage
- Budget vs actual: compare planned budget to actual spend at project, phase, and deliverable level
- Cost components: labour cost (hours × rate), expense cost, and overhead allocation
- Margin trend: weekly or monthly margin trend for long-running projects
- Portfolio profitability: rank all active projects by profitability for executive overview
- Export: export project P&L to CSV or PDF for client or management reporting

### Advanced
- Phase-level profitability: break down margin by project phase to identify where cost overruns occur
- Resource cost contribution: show each team member's cost contribution to project total
- Budget burn rate: days-to-budget-exhaustion estimate based on current spend rate
- Historical comparison: compare margin on this project to similar past projects
- Cost category breakdown: split costs into direct labour, subcontractor, expenses, and overhead

### AI-Powered
- Margin risk prediction: flag projects where current burn rate suggests the budget will be exhausted before delivery is complete
- Optimisation suggestions: recommend phase reallocation or resource swaps to improve margin
- Benchmark comparison: compare project margin against industry benchmarks for the engagement type

---

## Data Model

```erDiagram
    psa_profitability_snapshots {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        date snapshot_date
        decimal contract_value
        decimal total_cost
        decimal gross_profit
        decimal gross_margin_percent
        decimal budget_remaining
        json cost_breakdown
        timestamps created_at_updated_at
    }
```

| Table | Purpose | Key Columns |
|---|---|---|
| `psa_profitability_snapshots` | Aggregated project financials | `id`, `company_id`, `project_id`, `snapshot_date`, `gross_profit`, `gross_margin_percent`, `budget_remaining` |

Note: Profitability is computed from `time_entries`, `psa_allocations`, and `psa_projects` via read-optimised aggregation queries.

---

## Permissions

```
psa.profitability.view-own-projects
psa.profitability.view-all-projects
psa.profitability.view-cost-detail
psa.profitability.export
psa.profitability.view-portfolio
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `ProjectProfitabilityPage`, `PortfolioMarginPage`, `PhaseCostBreakdownPage`
- **Widgets:** `PortfolioMarginWidget`, `AtRiskProjectsWidget`, `BudgetBurnWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | Mavenlink | Deltek | Teamwork |
|---|---|---|---|---|
| Project P&L | Yes | Yes | Yes | No |
| Phase-level margin | Yes | Yes | Yes | No |
| AI margin risk prediction | Yes | No | No | No |
| Native time + billing data | Yes | Yes | Yes | Partial |
| Included in platform | Yes | No | No | No |

---

## Related

- [[project-delivery]] — contract value and project data source
- [[time-billing]] — labour hours and rates feed cost calculations
- [[resource-planning]] — resource cost rates
- [[capacity-planning]] — bench cost informs portfolio profitability
