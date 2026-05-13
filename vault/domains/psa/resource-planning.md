---
type: module
domain: Professional Services (PSA)
panel: psa
module-key: psa.resources
status: planned
color: "#4ADE80"
---

# Resource Planning

> Resource allocation across client projects — utilisation tracking, availability management, and skill-based assignment.

**Panel:** `psa`
**Module key:** `psa.resources`

---

## What It Does

Resource Planning gives professional services firms visibility into who is working on what and how fully utilised each team member is. Delivery managers see a resource calendar showing each person's project allocations over time, identify gaps and over-allocations, and assign people to projects based on their skills and availability. Utilisation reports compare billable hours to total working hours, highlighting under-used bench capacity and over-stretched individuals.

---

## Features

### Core
- Resource registry: all billable team members with role, skills, and standard hourly rate
- Project allocations: assign a team member to a project with a percentage allocation and date range
- Resource calendar: Gantt-style view of all team members' allocations over a rolling period
- Utilisation tracking: billable vs non-billable hours ratio per person and per period
- Skill-based search: find available team members by skill, role, or availability window
- Conflict detection: flag over-allocation when a resource is assigned beyond 100% on overlapping projects

### Advanced
- Named vs un-named resource booking: reserve a capacity slot before a specific person is confirmed
- Soft vs hard booking: distinguish tentative allocations from confirmed ones
- Leave integration: automatically exclude leave days from available capacity
- Allocation history: view past allocations for a team member for performance and billing review
- Role-based allocation: assign a role placeholder first, then fill with a named person later

### AI-Powered
- Resource recommendation: suggest the best-fit available person for a project requirement based on skills and utilisation
- Utilisation risk alert: flag team members trending toward burnout (consistently above 90% billable)
- Bench cost calculation: estimate the cost of bench time across the team

---

## Data Model

```erDiagram
    psa_resources {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        string role
        json skills
        decimal standard_rate
        timestamps created_at_updated_at
    }

    psa_allocations {
        ulid id PK
        ulid project_id FK
        ulid resource_id FK
        ulid company_id FK
        date start_date
        date end_date
        decimal allocation_percent
        string booking_type
        timestamps created_at_updated_at
    }

    psa_resources ||--o{ psa_allocations : "allocated via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `psa_resources` | Team members | `id`, `company_id`, `employee_id`, `role`, `skills`, `standard_rate` |
| `psa_allocations` | Project assignments | `id`, `project_id`, `resource_id`, `start_date`, `end_date`, `allocation_percent`, `booking_type` |

---

## Permissions

```
psa.resources.view-any
psa.resources.manage-allocations
psa.resources.view-utilisation
psa.resources.manage-registry
psa.resources.export
```

---

## Filament

- **Resource:** `App\Filament\Psa\Resources\PsaResourceResource`
- **Pages:** `ListPsaResources`, `ViewPsaResource`
- **Custom pages:** `ResourceCalendarPage`, `UtilisationReportPage`
- **Widgets:** `UtilisationSummaryWidget`, `OverAllocatedWidget`
- **Nav group:** Resources

---

## Displaces

| Feature | FlowFlex | Harvest Forecast | Teamwork | Float |
|---|---|---|---|---|
| Resource calendar | Yes | Yes | Yes | Yes |
| Skill-based search | Yes | No | No | No |
| Soft/hard booking | Yes | No | Yes | Yes |
| AI resource recommendation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[project-delivery]] — allocations reference PSA projects
- [[capacity-planning]] — forward-looking view of resource demand
- [[time-billing]] — actual time logged vs allocation
- [[profitability]] — resource cost feeds into profitability calculation
