---
type: module
domain: Projects & Work
panel: projects
module-key: projects.okrs
status: planned
color: "#4ADE80"
---

# OKRs

Objectives and Key Results: company-level and team-level goal setting with progress tracking and quarterly check-ins.

## Core Features

- Objective: title, owner, quarter, year, description, parent objective (nested OKRs)
- Key Results: measurable outcomes linked to an objective (target value, current value, unit)
- Progress calculation: KR progress auto-calculates objective progress as average of KR completion
- Status indicators: on-track (green), at-risk (amber), off-track (red) based on progress vs time elapsed
- Check-in: weekly/fortnightly progress update on each KR with notes
- Hierarchy: company → department → team → individual OKRs
- OKR dashboard: progress overview, health distribution, recent check-ins

## Data Model

| Table | Key Columns |
|---|---|
| `proj_objectives` | company_id, title, description, owner_id, quarter, year, parent_objective_id, progress_percent |
| `proj_key_results` | company_id, objective_id, title, target_value, current_value, unit, progress_percent |
| `proj_okr_checkins` | company_id, key_result_id, user_id, current_value, notes, checked_in_at |

## Filament

**Nav group:** OKRs

- `ObjectiveResource` — tree view (nested objectives), create, edit, view
- `OkrDashboardPage` (custom page) — quarterly overview with progress charts

## Related

- [[domains/projects/projects]]
- [[domains/hr/hr-analytics]]
