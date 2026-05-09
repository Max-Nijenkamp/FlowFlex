---
type: moc
domain: Projects & Work
panel: projects
cssclasses: domain-projects
phase: 2
color: "#4F46E5"
last_updated: 2026-05-08
---

# Projects & Work — Map of Content

Everything teams do day-to-day. Tasks, planning, documents, collaboration, time tracking. The Jira + Notion + Google Drive replacement.

**Panel:** `projects`  
**Phase:** 2 (core) · 8 (extensions)  
**Migration Range:** `150000–199999`  
**Colour:** Indigo `#4F46E5` / Light: `#EEF2FF`  
**Icon:** `heroicon-o-rectangle-stack`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[task-management\|Task Management]] | 2 | planned | Tasks, sub-tasks, assignees, priorities, dependencies |
| [[project-time-tracking\|Project Time Tracking]] | 2 | planned | Timer, timesheets, approval flow, billable hours |
| [[kanban-boards\|Kanban Boards]] | 2 | planned | Drag-drop cards, WIP limits, swimlanes |
| [[gantt-timeline\|Gantt & Timeline]] | 2 | planned | Dependencies, critical path, milestones, baseline |
| [[sprint-agile\|Sprint & Agile]] | 3 | planned | Scrum sprints, backlog, burndown, velocity |
| [[project-templates\|Project Templates]] | 2 | planned | Reusable project structures with relative dates |
| [[project-budget-costs\|Project Budget & Costs]] | 3 | planned | Labour cost tracking, expenses, EVM, profitability |
| Document Management | 2 | 📅 planned | File storage, versioning, permissions, search |
| Document Approvals & E-Sign | 8 | planned | Approval workflows, built-in e-signature |
| Team Collaboration | 8 | planned | Comments, @mentions, activity feeds |
| Resource & Capacity Planning | 8 | planned | Workload heatmap, availability calendar |
| OKR & Goal Management | 8 | planned | Company→Individual cascade, auto-update, grading |
| Portfolio Management | 8 | planned | Cross-project view, budget rollup, risk register |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `TaskCompleted` | Task Management | Project Planning (progress), Finance (milestone invoice) |
| `ProjectMilestoneReached` | Project Planning | Finance (trigger invoice), CRM (update deal) |
| `TimeEntryApproved` | Time Tracking | Payroll (add to run), Finance (client billing) |
| `DocumentSigned` | Document Approvals | Downstream workflow trigger |

---

## Permissions Prefix

`projects.tasks.*` · `projects.time.*` · `projects.documents.*`  
`projects.planning.*` · `projects.wiki.*` · `projects.resources.*`

---

## Competitors Displaced

Jira · Asana · Monday.com · Notion · ClickUp · Google Drive · Linear

---

## Related

- [[MOC_Domains]]
- [[entity-project]]
- [[MOC_HR]] — time entries → payroll
- [[MOC_Finance]] — billable hours → client billing
- [[MOC_CRM]] — project milestones → deal status
