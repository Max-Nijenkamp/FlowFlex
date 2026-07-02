---
domain: projects
module: templates
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Templates — API / DTOs

## Input DTOs

### CreateFromTemplateData
`template_id`, `project_name` (required), `start_date` (required), `owner_id`, `member_ids[]`.

### SaveAsTemplateData
`project_id`, `name`, `category`.

## Output

- `instantiate` → `ProjectData` (the newly created project, via projects.projects).
- `fromProject` → `TemplateData` (id, name, category, section/task/milestone counts).

## Public / Portal Endpoints

None.
