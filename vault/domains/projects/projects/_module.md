---
domain: projects
module: projects
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Projects

Project records with goals, ownership, status, team members, and budget tracking. The top-level container for all work — the Projects domain anchor, built first in `/projects`.

## Module-key

`projects.projects`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.projects`  
**Tables:** `proj_projects`, `proj_project_members`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | client association (account/contact link); internal-only projects without it |
| Soft | [[../tasks/_module\|projects.tasks]], [[../time-tracking/_module\|projects.time]] | health/progress + actual-vs-estimate inputs |

## Core Features

- Project record: name, description, status, start date, target date, owner, team members.
- Status machine `planning → active → on_hold → completed | cancelled` (spatie/laravel-model-states).
- Categories/tags for grouping (spatie/laravel-tags).
- Budget: estimated hours, estimated cost, actual-vs-estimate (actuals from time entries when active).
- Health indicators: on-track / at-risk / off-track — completion % vs timeline elapsed % (at-risk when >15pt behind *(assumed)*).
- Client association to a CRM account/contact (read-only via CRM service).
- Member roles gate visibility: viewer/member/owner — non-members don't see private projects.

## See features/

- [[features/project-record|Project Record & Health]] — CRUD, health math, dashboard.
- [[features/project-membership|Membership & Visibility]] — member roles gate project access.

## Build Manifest

```
database/migrations/xxxx_create_proj_projects_table.php
database/migrations/xxxx_create_proj_project_members_table.php
app/Models/Projects/{Project,ProjectMember}.php
app/States/Projects/Project/{ProjectState,Planning,Active,OnHold,Completed,Cancelled}.php
app/Data/Projects/{CreateProjectData,ProjectData}.php
app/Contracts/Projects/ProjectServiceInterface.php · app/Services/Projects/ProjectService.php
app/Providers/Projects/ProjectsServiceProvider.php
app/Filament/Projects/Resources/ProjectResource.php · Widgets/ProjectStatsWidget.php
database/factories/Projects/{ProjectFactory,ProjectMemberFactory}.php
tests/Feature/Projects/{ProjectTest,ProjectVisibilityTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see/edit company B's projects or members.
- [ ] Module gating: artifacts hidden when `projects.projects` inactive.
- [ ] Non-member cannot see project without `view-any`.
- [ ] Health math fixtures (on-track / at-risk / off-track boundaries).
- [ ] Target before start rejected.
- [ ] State transitions per machine; complete sets `completed_at`.
- [ ] Actuals zero when time module inactive (no error).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `ContactService` (read-only) | crm.contacts | Client association; internal-only projects without it |
| Reads | time-entry actuals | projects.time | `ProjectService::actuals()`; 0 when module inactive |

**Data ownership:** `projects.projects` writes only `proj_projects` + `proj_project_members`. The CRM client link is a stored foreign id resolved via CRM's read API — never a write into `crm_*` tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../sprints/_module|Sprints]] · [[../../crm/contacts/_module|CRM Contacts]]
- Client link resolves via CRM read API; no `crm_accounts` table dependency in v1
- [[../../../glossary]]
