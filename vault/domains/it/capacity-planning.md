---
type: module
domain: IT & Security
panel: it
module-key: it.capacity
status: planned
color: "#4ADE80"
---

# Capacity Planning

> Monitor infrastructure capacity metrics, project growth against current resources, and plan upgrade investments before performance degrades.

**Panel:** `it`
**Module key:** `it.capacity`

## What It Does

Capacity Planning gives IT managers a forward-looking view of whether current infrastructure can support projected business growth. Current utilisation metrics (CPU, memory, storage, network bandwidth) are collected for each infrastructure resource. Growth projections are applied based on headcount or transaction volume forecasts. The module highlights where utilisation will exceed safe thresholds within a configurable planning horizon and helps build a business case for upgrade investments with cost and lead-time estimates.

## Features

### Core
- Infrastructure resource register: servers, storage arrays, network devices, and cloud instances — with current capacity specifications
- Utilisation metrics: current CPU %, memory %, storage used/total, network throughput — entered manually or imported from monitoring tools
- Utilisation trending: 90-day history of utilisation per resource; chart showing trajectory
- Threshold alerts: notify IT manager when utilisation exceeds a configurable warning threshold (e.g., storage >80%)
- Resource status: healthy, warning, critical — based on current utilisation vs threshold

### Advanced
- Growth projections: model utilisation growth as a percentage per month or linked to a business metric (headcount, transaction volume)
- Runway calculation: at current growth rate, how many months until a resource hits the critical threshold?
- Upgrade scenarios: model the impact of an upgrade (add storage, migrate to larger instance) on the runway calculation
- Cloud resource recommendations: for cloud instances, compare current vs recommended instance type based on utilisation
- Cost projection: estimate cost of upgrade options with vendor pricing input
- Capacity review board: monthly summary of all resources approaching threshold for IT management review

### AI-Powered
- Anomalous consumption alert: flag resources showing unexpected utilisation spikes that could indicate a runaway process or security incident
- Upgrade timing recommendation: suggest the optimal upgrade window based on cost, lead time, and business risk of delay

## Data Model

```erDiagram
    it_infrastructure_resources {
        ulid id PK
        ulid company_id FK
        string name
        string resource_type
        string location
        json capacity_spec
        string status
        decimal warning_threshold_pct
        decimal critical_threshold_pct
        timestamps timestamps
    }

    it_capacity_metrics {
        ulid id PK
        ulid resource_id FK
        string metric_type
        decimal value
        decimal capacity_total
        decimal utilisation_pct
        date recorded_on
    }

    it_capacity_projections {
        ulid id PK
        ulid resource_id FK
        decimal monthly_growth_pct
        integer months_to_critical
        date projected_critical_date
        decimal upgrade_cost_estimate
        text notes
        timestamps timestamps
    }

    it_infrastructure_resources ||--o{ it_capacity_metrics : "tracks"
    it_infrastructure_resources ||--o{ it_capacity_projections : "projected by"
```

| Table | Purpose |
|---|---|
| `it_infrastructure_resources` | Infrastructure resource register |
| `it_capacity_metrics` | Historical utilisation readings |
| `it_capacity_projections` | Growth models and upgrade scenarios |

## Permissions

```
it.capacity.view-any
it.capacity.manage-resources
it.capacity.record-metrics
it.capacity.manage-projections
it.capacity.export
```

## Filament

**Resource class:** `InfrastructureResourceResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `CapacityOverviewPage` (all resources with RAG status and runway), `UpgradeScenarioPage`
**Widgets:** `CapacityAtRiskWidget` (resources in warning or critical state)
**Nav group:** Assets

## Displaces

| Competitor | Feature Replaced |
|---|---|
| SolarWinds Capacity Planning | Infrastructure capacity management |
| Nlyte (lite) | Data centre capacity planning |
| Apptio Cloudability (SMB) | Cloud resource sizing and cost optimisation |
| ManageEngine OpManager | Infrastructure capacity monitoring |

## Related

- [[asset-management]] — infrastructure hardware assets listed here
- [[it-analytics]] — capacity metrics surface in IT analytics dashboards
- [[../finance/INDEX]] — upgrade cost projections linked to capex planning
