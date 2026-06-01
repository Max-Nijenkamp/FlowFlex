---
type: module
domain: Projects & Work
panel: projects
module-key: projects.templates
status: planned
color: "#4ADE80"
---

# Project Templates

Reusable project and task list templates. Create a new project pre-populated with sections, tasks, and milestone structure from a template.

## Core Features

- Template record: name, description, category, default duration (days)
- Template sections: ordered list of sections/columns
- Template tasks: pre-defined tasks per section (title, description, estimated hours, order)
- Template milestones: pre-defined milestone checkpoints
- Create project from template: all sections, tasks, and milestones copied; start date used to calculate due dates
- Date offset: template tasks use day-offset from project start (e.g. task due on Day+5)
- System templates: built-in templates for common project types (Software Sprint, Event Planning, Onboarding)
- Save existing project as template

## Data Model

| Table | Key Columns |
|---|---|
| `proj_templates` | company_id, name, description, category, default_duration_days, is_system |
| `proj_template_sections` | template_id, company_id, name, order |
| `proj_template_tasks` | template_id, section_id, company_id, title, description, estimated_hours, day_offset, order |
| `proj_template_milestones` | template_id, company_id, title, day_offset |

## Filament

**Nav group:** Settings

- `ProjectTemplateResource` — list, create, edit templates and their sections/tasks
- `CreateProjectFromTemplatePage` (custom page) — select template, enter project name + start date, confirm

## Related

- [[domains/projects/projects]]
- [[domains/projects/tasks]]
