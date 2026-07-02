---
domain: projects
module: templates
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Project Templates

Reusable project and task-list templates. Create a new project pre-populated with sections, tasks, and milestone structure from a template.

## Module-key

| Field | Value |
|---|---|
| key | `projects.templates` |
| priority | p2 |
| panel | projects |
| permission-prefix | `projects.templates` |
| tables | `proj_templates`, `proj_template_sections`, `proj_template_tasks`, `proj_template_milestones` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../projects/_module\|projects.projects]] + [[../tasks/_module\|projects.tasks]] + [[../milestones/_module\|projects.milestones]] | instantiation creates all three |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |

## Core Features

- Template record: name, description, category, default duration (days).
- Template sections (ordered), tasks (per section: title, estimate, order, day-offset), milestones (day-offset).
- Create project from template: sections/tasks/milestones copied; start date → due dates.
- Date offset: day-offset from project start (calendar days v1 *(assumed)*).
- System templates: built-in (Software Sprint, Event Planning, Onboarding) — seeded, read-only, company-copy-on-edit *(assumed)*.
- Save an existing project as a template.

## See features/

- [[features/template-authoring|Template Authoring]] — build/edit templates + system read-only rows.
- [[features/instantiate-project|Instantiate from Template]] — the wizard that materialises a project.

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
app/Filament/Projects/Resources/ProjectTemplateResource.php · Pages/CreateProjectFromTemplatePage.php
database/factories/Projects/ProjectTemplateFactory.php
tests/Feature/Projects/TemplateInstantiationTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Instantiate copies sections/tasks/milestones with correct date offsets.
- [ ] Instantiation atomic (failure rolls all back).
- [ ] System template not editable; visible to all companies.
- [ ] Save-as-template computes offsets from project start.
- [ ] Company A template invisible to company B.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | project/task/milestone creation | projects.projects / tasks / milestones | instantiation delegates to owning modules' actions |

**Data ownership:** `projects.templates` writes only its four `proj_template_*` tables. Instantiation creates real projects/tasks/milestones **through those modules' owning actions**, never by writing `proj_projects`/`proj_tasks`/`proj_milestones` directly ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../projects/_module|Projects]] · [[../tasks/_module|Tasks]] · [[../milestones/_module|Milestones]]
- [[../../../glossary]]
