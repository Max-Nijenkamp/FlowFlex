---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.templates
status: planned
priority: p2
depends-on: [projects.projects, projects.tasks, projects.milestones, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [proj_templates, proj_template_sections, proj_template_tasks, proj_template_milestones]
permission-prefix: projects.templates
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Project Templates

Reusable project and task list templates. Create a new project pre-populated with sections, tasks, and milestone structure from a template.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/projects\|projects.projects]] + [[domains/projects/tasks\|projects.tasks]] + [[domains/projects/milestones\|projects.milestones]] | instantiation creates all three |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Template record: name, description, category, default duration (days)
- Template sections: ordered list of sections/columns
- Template tasks: pre-defined tasks per section (title, description, estimated hours, order)
- Template milestones: pre-defined milestone checkpoints
- Create project from template: all sections, tasks, and milestones copied; start date used to calculate due dates
- Date offset: template tasks use day-offset from project start (e.g. task due on Day+5; weekends skipped *(assumed: calendar days v1)*)
- System templates: built-in templates for common project types (Software Sprint, Event Planning, Onboarding) — sushi/seeded, read-only, company-copy-on-edit *(assumed)*
- Save existing project as template

---

## Data Model

### proj_templates — id, company_id (indexed), name, description, category, default_duration_days, is_system (boolean), deleted_at
### proj_template_sections — id, template_id FK, company_id, name, order
### proj_template_tasks — id, template_id FK, section_id FK, company_id, title, description, estimated_hours, day_offset, order
### proj_template_milestones — id, template_id FK, company_id, title, day_offset

System templates: `company_id` null + global read scope exception *(assumed: sushi-style seeded rows readable by all, never editable)*.

---

## DTOs

### CreateFromTemplateData — template_id, project_name (required), start_date (required), owner_id, member_ids[]
### SaveAsTemplateData — project_id, name, category

## Services & Actions

- `TemplateService::instantiate(CreateFromTemplateData $data): ProjectData` — single transaction: project + sections + tasks (due = start + day_offset) + milestones; delegates creation to owning modules' actions
- `TemplateService::fromProject(SaveAsTemplateData $data): TemplateData` — reverse: offsets computed from project start

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ProjectTemplateResource` | #1 CRUD resource | sections/tasks repeaters; system templates read-only |
| `CreateProjectFromTemplatePage` | #7 wizard custom page | template → name/start date → confirm |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('projects.templates.view-any') && BillingService::hasModule('projects.templates')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Tenancy** (medium): Document the exact scope override: a read-only global scope exception that surfaces is_system/company_id-null rows to all tenants while blocking any cross-tenant write/edit; reference multi-tenancy.md.

---

## Permissions

`projects.templates.view-any` · `projects.templates.create` · `projects.templates.update` · `projects.templates.instantiate`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Instantiate copies sections/tasks/milestones with correct date offsets
- [ ] Instantiation atomic (failure rolls all back)
- [ ] System template not editable; visible to all companies
- [ ] Save-as-template computes offsets from project start
- [ ] Company A template invisible to company B

---

## Build Manifest

```
database/migrations/xxxx_create_proj_templates_table.php
database/migrations/xxxx_create_proj_template_sections_table.php
database/migrations/xxxx_create_proj_template_tasks_table.php
database/migrations/xxxx_create_proj_template_milestones_table.php
app/Models/Projects/{ProjectTemplate,TemplateSection,TemplateTask,TemplateMilestone}.php
app/Data/Projects/{CreateFromTemplateData,SaveAsTemplateData}.php
app/Services/Projects/TemplateService.php
database/seeders/SystemProjectTemplatesSeeder.php
app/Filament/Projects/Resources/ProjectTemplateResource.php
app/Filament/Projects/Pages/CreateProjectFromTemplatePage.php
database/factories/Projects/ProjectTemplateFactory.php
tests/Feature/Projects/TemplateInstantiationTest.php
```

---

## Related

- [[domains/projects/projects]]
- [[domains/projects/tasks]]
