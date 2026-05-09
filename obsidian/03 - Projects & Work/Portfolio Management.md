---
tags: [flowflex, domain/projects, portfolio, multi-project, phase/8]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-08
---

# Portfolio Management

A bird's-eye view across all projects. One screen to see what's on track, what's at risk, where the bottlenecks are, and how your entire programme budget is tracking. No more asking 8 project managers for status updates before the board meeting. Replaces Monday.com Portfolios and Asana Portfolios.

**Who uses it:** PMO managers, executives, department heads, programme managers
**Filament Panel:** `projects`
**Depends on:** Core, [[Task Management]], [[Project Planning]], [[Resource & Capacity Planning]], [[Time Tracking]]
**Phase:** 8

---

## Features

### Portfolio View

- Create portfolios: a named collection of related projects (e.g. "2026 Tech Initiatives", "Q3 Product Launches")
- Add projects manually or via filter rules (e.g. auto-include all projects tagged `department:engineering`)
- Dashboard: status grid showing all projects with RAG status, health score, owner, due date
- Timeline view: all project timelines on one Gantt-style horizontal chart (read-only overview)
- Workload view: aggregated resource usage across all portfolio projects

### Portfolio Health Scoring

- Per-project health score: composite of task completion rate, milestone on-time rate, budget variance, team velocity
- Portfolio-level roll-up: average health across all projects
- Trend: health over past 4 weeks (improving/stable/deteriorating)
- AI narrative: "3 projects at risk — engineering capacity is the common blocker"

### Status Reporting

- Weekly status report: auto-generated summary per project (key milestones this week, risks, blockers)
- Portfolio digest: email digest to stakeholders every Monday with colour-coded status table
- Executive view: one-page portfolio summary PDF exportable for board packs
- Custom status fields: add your own status indicators (budget RAG, scope change flag, exec sponsor alert)

### Budget Tracking

- Assign budget to each project: total approved budget in chosen currency
- Track actuals: time-logged hours × billing rate + external expenses
- Budget variance: remaining budget, % spent, projected final cost (based on current burn rate)
- Portfolio budget roll-up: total budget, total committed, total remaining across all projects
- Budget alerts: notify PM when 80%/100% of budget consumed

### Milestone Tracking

- Milestone list across all portfolio projects in one view
- Filter: upcoming this month, at-risk, missed
- Dependency chains: cross-project milestone dependencies (Project B starts when Project A's phase 1 completes)
- Milestone calendar: all milestones on shared calendar view

### Risk & Issue Register

- Portfolio-level risk register: risks that affect multiple projects
- Escalated issues: issues flagged as portfolio-level from individual projects
- Risk heatmap: likelihood vs impact matrix for all open risks
- Owner assignment: each risk/issue has an owner and review date

### Capacity Forecasting

- Aggregated demand: total resource hours required across all portfolio projects per week
- Supply: available hours per team member (from [[Resource & Capacity Planning]])
- Gap view: weeks where demand exceeds supply, with drill-down to which projects
- Scenario planning: add/remove projects from portfolio to model capacity impact

---

## Database Tables (3)

### `project_portfolios`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `owner_id` | ulid FK | |
| `budget` | decimal nullable | |
| `budget_currency` | string nullable | |
| `status` | enum | `active`, `on_hold`, `closed` |
| `health_score` | decimal nullable | 0-100, computed |

### `project_portfolio_items`
| Column | Type | Notes |
|---|---|---|
| `portfolio_id` | ulid FK | |
| `project_id` | ulid FK | |
| `budget_allocation` | decimal nullable | if split from portfolio budget |
| `sort_order` | integer | |

### `project_portfolio_risks`
| Column | Type | Notes |
|---|---|---|
| `portfolio_id` | ulid FK | |
| `title` | string | |
| `description` | text nullable | |
| `likelihood` | enum | `low`, `medium`, `high` |
| `impact` | enum | `low`, `medium`, `high` |
| `owner_id` | ulid FK nullable | |
| `status` | enum | `open`, `mitigated`, `closed` |
| `review_date` | date nullable | |

---

## Permissions

```
projects.portfolios.view
projects.portfolios.create
projects.portfolios.manage
projects.portfolios.view-budgets
projects.portfolios.export-report
```

---

## Competitor Comparison

| Feature | FlowFlex | Monday.com Portfolios | Asana Portfolios | MS Project |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (Enterprise only) | ❌ (Business+) | ❌ (€20+/user/mo) |
| Budget tracking | ✅ | ✅ | ✅ | ✅ |
| AI portfolio narrative | ✅ | ❌ | ❌ | ❌ |
| Cross-project milestone deps | ✅ | partial | partial | ✅ |
| Capacity aggregation | ✅ | partial | ✅ | ✅ |
| Executive PDF export | ✅ | ✅ | ✅ | ✅ |

---

## Related

- [[Projects Overview]]
- [[Project Planning]]
- [[Resource & Capacity Planning]]
- [[Task Management]]
- [[Time Tracking]]
- [[OKR & Goal Management]]
