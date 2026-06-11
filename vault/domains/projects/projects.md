---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.projects
status: planned
priority: p2
depends-on: [core.billing, core.rbac]
soft-depends: [crm.contacts, projects.tasks, projects.time]
fires-events: []
consumes-events: []
patterns: [states, service, money]
tables: [proj_projects, proj_project_members]
permission-prefix: projects.projects
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Projects

Project records with goals, ownership, status, team members, and budget tracking. The top-level container for all work — the Projects domain anchor, build first in `/projects`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/crm/contacts\|crm.contacts]] | client association (account/contact link); internal-only projects without it |
| Soft | [[domains/projects/tasks\|projects.tasks]], [[domains/projects/time-tracking\|projects.time]] | health/progress + actual-vs-estimate inputs |

---

## Core Features

- Project record: name, description, status, start date, target date, owner, team members
- Project status machine: `planning → active → on_hold → completed | cancelled` (spatie/laravel-model-states)
- Project categories/tags for grouping (spatie/laravel-tags)
- Budget: estimated hours, estimated cost, actual vs estimate tracking (actuals from time entries when active)
- Project health indicators: on-track, at-risk, off-track — task completion % vs timeline elapsed % (at-risk when >15pt behind *(assumed)*)
- Client association: link project to a CRM account or contact
- Project dashboard: overview of active projects, completion rate, health summary
- Archive completed projects
- Member roles gate project visibility: viewer/member/owner — non-members don't see private projects *(assumed: projects visible to members only; `projects.projects.view-any` sees all)*

---

## Data Model

### proj_projects

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| name | string | not null | |
| description | text | nullable | |
| status | string | default `planning` | state machine |
| start_date / target_date | date | target ≥ start | |
| completed_at | timestamp | nullable | |
| owner_id | ulid | not null FK users | |
| client_account_id | ulid | nullable | CRM link |
| estimated_hours | int | nullable | |
| estimated_cost_cents | bigint | nullable | |
| color | string(7) | default per palette *(assumed)* | board/gantt display |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, status)`, `(company_id, owner_id)`

### proj_project_members

| Column | Type | Notes |
|---|---|---|
| id, project_id FK, company_id, user_id FK | ulid | unique `(project_id, user_id)` |
| role | string | owner / member / viewer |

---

## State Machine

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `planning` | `active` | `projects.projects.update` | |
| `active` | `on_hold` / `completed` / `cancelled` | `projects.projects.update` (complete requires all tasks done or explicit confirm *(assumed: confirm modal)*) | completed sets `completed_at` |
| `on_hold` | `active` / `cancelled` | | |

Audited.

---

## DTOs

### CreateProjectData — name (required, max:200), description?, start_date/target_date (target ≥ start — "Target date must be on or after the start date."), owner_id (in company), client_account_id?, estimated_hours?, estimated_cost_cents?, member_ids[]
### ProjectData (output) — id, name, status, dates, owner_name, client_name, progress_percent, health (on-track/at-risk/off-track), estimated vs actual hours/cost

## Services & Actions

Interface→Service: `ProjectServiceInterface` → `ProjectService`.

- `create(CreateProjectData $data): ProjectData` — creator auto-added as owner member
- `health(string $projectId): string` — completion vs elapsed math
- `actuals(string $projectId): array{hours: float, cost_cents: int}` — from time entries (0 when module inactive)
- `addMember(string $projectId, string $userId, string $role)` / `removeMember(...)`

---

## Filament

**Nav group:** Projects

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ProjectResource` | #1 CRUD resource | member-scoped listing; status filters |
| Project view page | #2 detail with tabs | Overview, Tasks, Sprints, Milestones, Files, Time Entries (soft-dep tabs conditional) |
| `ProjectStatsWidget` | #6 widget | active count, health pie |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('projects.projects.view-any') && BillingService::hasModule('projects.projects')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`projects.projects.view-any` · `projects.projects.view` · `projects.projects.create` · `projects.projects.update` · `projects.projects.delete` · `projects.projects.manage-members`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Non-member cannot see project without `view-any`
- [ ] Health math fixtures (on-track / at-risk / off-track boundaries)
- [ ] Target before start rejected
- [ ] State transitions per machine; complete sets completed_at
- [ ] Actuals zero when time module inactive (no error)

---

## Build Manifest

```
database/migrations/xxxx_create_proj_projects_table.php
database/migrations/xxxx_create_proj_project_members_table.php
app/Models/Projects/{Project,ProjectMember}.php
app/States/Projects/Project/{ProjectState,Planning,Active,OnHold,Completed,Cancelled}.php
app/Data/Projects/{CreateProjectData,ProjectData}.php
app/Contracts/Projects/ProjectServiceInterface.php
app/Services/Projects/ProjectService.php
app/Providers/Projects/ProjectsServiceProvider.php
app/Filament/Projects/Resources/ProjectResource.php
app/Filament/Projects/Widgets/ProjectStatsWidget.php
database/factories/Projects/{ProjectFactory,ProjectMemberFactory}.php
tests/Feature/Projects/{ProjectTest,ProjectVisibilityTest}.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/sprints]]
- [[domains/crm/contacts]]
