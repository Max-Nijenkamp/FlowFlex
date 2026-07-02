---
domain: projects
module: templates
feature: template-authoring
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Template Authoring

Build and edit reusable project templates; save an existing project as a template.

## Behaviour

- Create/edit a template: name, category, default duration; ordered sections; per-section tasks (title, estimate, day-offset, order); milestones (day-offset).
- Save-as-template from a live project: offsets computed from the project start.
- System templates are read-only; editing one copies it into the company (copy-on-edit).

## UI

- **Kind**: simple-resource (template CRUD with repeaters).
- **Page**: `ProjectTemplateResource` at `/app/projects/templates` (nav group Settings).
- **Layout**: form with section repeater → nested task repeater; milestone repeater; category + duration fields. System templates render read-only with a "Duplicate to edit" action.
- **Key interactions**: add/reorder sections + tasks; save-as-template action on a project; duplicate a system template.
- **States**: empty (no company templates → system starters shown) · loading · error (edit system → "Duplicate this template to customise it") · selected (template row).
- **Gating**: `projects.templates.view-any`; create/edit `create`/`update`.

## Data

- Owns / writes: `proj_templates`, `proj_template_sections`, `proj_template_tasks`, `proj_template_milestones`.
- Reads: a source project (for save-as-template) via projects.projects.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes / Feeds: nothing.
- Shared entity: `proj_projects` (read, for save-as-template).

## Unknowns

- Capturing dependencies/allocations; template versioning — see [[../unknowns]].

## Related

- [[../_module|Templates]] · [[instantiate-project|Instantiate from Template]]
