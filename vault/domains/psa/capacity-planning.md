---
type: module
domain: Professional Services (PSA)
panel: psa
module-key: psa.capacity
status: planned
color: "#4ADE80"
---

# Capacity Planning

> Team capacity forecasting — bench time visibility, future project demand modelling, and hiring signal generation.

**Panel:** `psa`
**Module key:** `psa.capacity`

---

## What It Does

Capacity Planning gives professional services leaders a forward-looking view of their team's available capacity against projected project demand. By combining confirmed and pipeline project resource requirements with current team headcount and allocation data, the module calculates surplus and deficit capacity by role, skill, and time period. This directly informs hiring decisions and pipeline prioritisation, and surfaces bench time that should be filled with training or business development activities.

---

## Features

### Core
- Capacity supply view: total available hours per role per month based on current headcount and working patterns
- Demand forecast: aggregate resource requirements from confirmed projects and probability-weighted pipeline projects
- Surplus and deficit view: net capacity position (supply minus demand) by role and time period
- Bench time tracking: identify team members with confirmed availability (bench time) in upcoming periods
- Pipeline project modelling: include CRM-linked opportunities in demand with probability weighting

### Advanced
- Hiring signal report: surface periods where capacity deficit exceeds a threshold as a hire recommendation
- Scenario modelling: compare capacity position under different win/loss scenarios for pipeline deals
- Leave and absence integration: incorporate planned leave in supply calculations automatically
- Contractor capacity: include external contractor capacity alongside internal team capacity
- Historical accuracy tracking: compare past capacity forecasts against actual utilisation for model improvement

### AI-Powered
- Win probability-adjusted demand: AI refines opportunity win probabilities for more accurate demand forecasting
- Optimal hiring timing: recommend the earliest start date for a new hire to address a projected deficit
- Bench recommendation: suggest which training or internal projects bench team members should be assigned to

---

## Data Model

```erDiagram
    capacity_snapshots {
        ulid id PK
        ulid company_id FK
        string role
        date period_start
        date period_end
        decimal supply_hours
        decimal demand_hours
        decimal net_capacity_hours
        json demand_sources
        timestamps created_at_updated_at
    }

    bench_assignments {
        ulid id PK
        ulid company_id FK
        ulid resource_id FK
        date start_date
        date end_date
        string activity_type
        text notes
        timestamps created_at_updated_at
    }

    capacity_snapshots }o--|| companies : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `capacity_snapshots` | Capacity forecasts | `id`, `company_id`, `role`, `period_start`, `supply_hours`, `demand_hours`, `net_capacity_hours` |
| `bench_assignments` | Bench time activities | `id`, `resource_id`, `start_date`, `end_date`, `activity_type` |

---

## Permissions

```
psa.capacity.view
psa.capacity.manage-bench
psa.capacity.view-hiring-signals
psa.capacity.run-scenarios
psa.capacity.export
```

---

## Filament

- **Resource:** None (read-only analytics and custom page)
- **Pages:** N/A
- **Custom pages:** `CapacityPlanningPage`, `BenchManagementPage`, `HiringSignalPage`, `ScenarioModellingPage`
- **Widgets:** `CapacityGapWidget`, `BenchTimeWidget`, `HiringSignalWidget`
- **Nav group:** Resources

---

## Displaces

| Feature | FlowFlex | Harvest Forecast | Mavenlink | Excel models |
|---|---|---|---|---|
| Supply vs demand by role | Yes | Yes | Yes | Manual |
| Pipeline demand weighting | Yes | No | Partial | Manual |
| Hiring signal generation | Yes | No | No | Manual |
| AI win probability adjustment | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[resource-planning]] — current allocations feed supply calculation
- [[project-delivery]] — confirmed project demand
- [[crm/INDEX]] — pipeline opportunities feed probability-weighted demand
- [[hr/INDEX]] — headcount data underpins supply calculation
