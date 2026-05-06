---
tags: [flowflex, domain/projects, overview, phase/2]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-06
---

# Projects Overview

The work management domain. Everything teams do day-to-day — tasks, planning, documents, collaboration — lives here. It's the Jira + Notion + Google Drive replacement that doesn't require a certification to understand.

**Filament Panel:** `projects`
**Domain Colour:** Indigo `#4F46E5` / Light: `#EEF2FF`
**Domain Icon:** `rectangle-stack` (Heroicons)
**Phase:** 2 (core: Task Management, Time Tracking, Document Management) + 5 (full suite)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[Task Management]] | 2 | Kanban, list, calendar, timeline, automations |
| [[Time Tracking]] | 2 | One-click timer, manual entry, approval |
| [[Document Management]] | 2 | File storage, versioning, permissions, search |
| [[Project Planning]] | 5 | Full Gantt, milestones, dependencies |
| [[Document Approvals & E-Sign]] | 5 | Approval workflows, built-in e-signature |
| [[Knowledge Base & Wiki]] | 5 | Block editor, SOPs, handbooks |
| [[Team Collaboration]] | 5 | Comments, @mentions, activity feeds |
| [[Resource & Capacity Planning]] | 5 | Workload heatmap, availability |
| [[Agile & Sprint Management]] | 5 | Scrum, backlog, burndown |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `TaskCompleted` | [[Task Management]] | [[Project Planning]] (updates progress), [[Invoicing]] (if milestone-linked) |
| `ProjectMilestoneReached` | [[Project Planning]] | [[Invoicing]] (trigger milestone invoice), CRM (update deal status) |
| `TimeEntryApproved` | [[Time Tracking]] | [[Payroll]] (add to pay run), [[Client Billing & Retainers]] (mark billable) |
| `DocumentSigned` | [[Document Approvals & E-Sign]] | Downstream workflow trigger |

## Related

- [[Task Management]]
- [[Time Tracking]]
- [[Document Management]]
- [[Project Planning]]
- [[Payroll]]
- [[Invoicing]]
- [[Client Billing & Retainers]]
- [[Panel Map]]
