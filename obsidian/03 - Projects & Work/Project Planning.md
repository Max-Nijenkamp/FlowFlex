---
tags: [flowflex, domain/projects, planning, gantt, milestones, phase/8]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-06
---

# Project Planning

For managing larger bodies of work with milestones, dependencies, and resource allocation. The Gantt layer on top of tasks.

**Who uses it:** Project managers, team leads
**Filament Panel:** `projects`
**Depends on:** [[Task Management]]
**Phase:** 8
**Build complexity:** Very High — 2 resources, 3 pages, 5 tables

## Events Fired

- `ProjectCreated`
- `ProjectMilestoneReached` → consumed by [[Invoicing]] (trigger milestone invoice), CRM (update deal status)
- `ProjectCompleted`
- `ProjectAtRisk` (schedule slippage detected)

## Features

- **Project creation wizard** — name, description, client (CRM link), start date, end date, budget (Finance link), team members
- **Gantt chart view** — tasks and milestones on a timeline
- **Milestone markers** — visual on Gantt, trigger invoice events when reached
- **Task dependencies** — Finish-to-Start, Start-to-Start, Finish-to-Finish, Start-to-Finish
- **Critical path detection** — highlights tasks that determine project end date
- **Baseline recording** — snapshot the original plan, compare against actuals
- **Project health indicator** — on track / at risk / delayed — auto-calculated from schedule
- **Project templates** — save a project structure as a reusable template
- **Portfolio view** — all projects, their status, RAG rating, budget vs actual
- **Project archive and search**

## Project Health Calculation

The system automatically sets project health based on:
- Is the completion % behind the expected schedule %?
- Are any critical path tasks overdue?
- Is the budget exceeded?

Status progression: On Track → At Risk → Delayed (fires `ProjectAtRisk` event)

## Database Tables (5)

1. `projects` — project records
2. `project_milestones` — milestone definitions and completion status
3. `project_templates` — reusable project structures
4. `project_baselines` — snapshotted plan data for comparison
5. `project_members` — team member assignments per project

## Related

- [[Projects Overview]]
- [[Task Management]]
- [[Time Tracking]]
- [[Resource & Capacity Planning]]
- [[Invoicing]]
- [[Client Billing & Retainers]]
- [[Agile & Sprint Management]]
