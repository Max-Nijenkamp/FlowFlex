---
type: module
domain: Projects & Work
panel: projects
module-key: projects.portfolios
status: planned
color: "#4ADE80"
---

# Portfolios

> Multi-project portfolio view — aggregate progress, resource allocation, milestone status, and time budget across all projects in one dashboard.

**Panel:** `projects`
**Module key:** `projects.portfolios`

## What It Does

Portfolios gives project managers and executives a cross-project overview without needing to open each project individually. A portfolio groups related projects (e.g. all Q2 initiatives, all client projects for Customer X). The portfolio dashboard shows aggregate progress, upcoming milestones across all projects, time logged vs budget, and resource allocation by person. It is a read-only aggregation view — all data is read from the underlying Tasks, Milestones, Time Tracking, and Sprints modules.

## Features

### Core
- Portfolio creation: group any set of projects into a named portfolio
- Portfolio dashboard: overall progress bar (% of tasks complete across all projects), list of projects with individual health indicators
- Project health indicators: RAG status (green/amber/red) computed from overdue tasks, overdue milestones, and time budget overrun
- Upcoming milestones: next 10 milestones across all portfolio projects sorted by target date
- Time summary: total hours logged vs total time budget across all projects

### Advanced
- Resource allocation view: per person — total hours logged across all portfolio projects vs their contracted available hours — spot over-allocation
- Portfolio budget vs actual: sum of project budgets vs sum of time-entry costs (hours × rate) across all projects
- Project status rollup: each project manager sets a weekly status (on track / at risk / blocked) with a comment — visible on portfolio dashboard without opening the project
- Portfolio filter: filter by project owner, department, status, or date range
- Export: download portfolio summary as PDF or CSV for executive reporting

### AI-Powered
- Portfolio health score: AI aggregates overdue task rate, milestone hit rate, and time budget overrun across all projects into a single portfolio health score (0–100)
- Dependency conflicts: AI identifies cross-project resource conflicts — person assigned to two projects both needing full capacity in the same sprint

## Data Model

```erDiagram
    proj_portfolios {
        ulid id PK
        ulid company_id FK
        string name
        string description
        ulid owner_id FK
        timestamps created_at/updated_at
    }

    proj_portfolio_projects {
        ulid portfolio_id FK
        ulid project_id FK
        timestamps created_at/updated_at
    }

    proj_project_status_updates {
        ulid id PK
        ulid project_id FK
        ulid company_id FK
        string rag_status
        text comment
        ulid submitted_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `rag_status` | on_track / at_risk / blocked |
| `proj_portfolio_projects` | Pivot — projects in a portfolio |
| All metrics | Computed from Tasks, Milestones, Time Tracking at render time |

## Permissions

- `projects.portfolios.view`
- `projects.portfolios.create`
- `projects.portfolios.edit`
- `projects.portfolios.submit-status`
- `projects.portfolios.view-resource-allocation`

## Filament

- **Resource:** `PortfolioResource`
- **Pages:** `ListPortfolios`, `ViewPortfolio` — dashboard with project health table, milestones, resources, budget
- **Custom pages:** None
- **Widgets:** `PortfolioHealthWidget` — RAG summary across all portfolios on main projects dashboard
- **Nav group:** Planning (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Monday.com Portfolios | Multi-project portfolio view |
| Asana Portfolios | Cross-project portfolio management |
| Smartsheet Portfolio Dashboard | Portfolio reporting |
| MS Project Portfolio | Enterprise project portfolio |

## Related

- [[tasks]]
- [[milestones]]
- [[time-tracking]]
- [[sprints]]
- [[gantt]]
