---
type: gap
severity: high
category: bug
status: resolved
color: "#F97316"
discovered: 2026-05-11
discovered_in: projects-resources
last_updated: 2026-05-11
---

# GAP-019: Projects resource dropdowns exposed cross-tenant data

## Context

Discovered during the Phase 0–2 security & quality audit on 2026-05-11. All six Projects Filament resources and EmployeeResource were reviewed for N+1 query risks and scoping.

## The Problem

Ten `Select::make()->options(fn () => Model::query()->pluck(...))` calls used the global Eloquent scope instead of explicitly scoping to the current company. Because the `BelongsToCompany` global scope relies on the CompanyContext singleton being set, these calls were technically filtered for authenticated sessions — but there is no guarantee `query()` hits the scope correctly in all Filament render paths (e.g. form open before SetCompanyContext middleware fires, or in unit test context). More critically, using plain `::query()` bypasses `withoutGlobalScopes()` + explicit `company_id` pattern that is the established convention for all dropdown/options closures in this codebase.

### Affected locations (before fix)

| Resource | Field | Model exposed |
|---|---|---|
| `ProjectResource` | `owner_id` | `User::query()` — all tenant users |
| `TaskResource` | `project_id` | `Project::query()` — all projects |
| `TaskResource` | `assignee_id` | `User::query()` — all tenant users |
| `SprintResource` | `project_id` | `Project::query()` — all projects |
| `KanbanBoardResource` | `project_id` | `Project::query()` — all projects |
| `ProjectMilestoneResource` | `project_id` | `Project::query()` — all projects |
| `TimeEntryResource` | `user_id` | `User::query()` — all tenant users |
| `TimeEntryResource` | `task_id` | `Task::query()` — all tasks |
| `TimeEntryResource` | `project_id` | `Project::query()` — all projects |
| `EmployeeResource` (department filter) | `department` filter | `Employee::withoutGlobalScopes()` without company_id — all tenants' departments |

## Impact

In a multi-tenant production environment, any authenticated user in one company could see project names, task titles, user emails, and department names belonging to other companies in form dropdowns. This is a data confidentiality breach.

## Resolution

All 10 calls replaced with `Model::withoutGlobalScopes()->where('company_id', app(CompanyContext::class)->currentId())->pluck(...)` pattern. Fix applied 2026-05-11.

### Fixed files

- `app/app/Filament/Projects/Resources/ProjectResource.php`
- `app/app/Filament/Projects/Resources/TaskResource.php`
- `app/app/Filament/Projects/Resources/SprintResource.php`
- `app/app/Filament/Projects/Resources/KanbanBoardResource.php`
- `app/app/Filament/Projects/Resources/ProjectMilestoneResource.php`
- `app/app/Filament/Projects/Resources/TimeEntryResource.php`
- `app/app/Filament/Hr/Resources/EmployeeResource.php`

## Links

- Related spec: [[projects-resources]]
- Session builder log: [[builder-log-projects-phase2]]
