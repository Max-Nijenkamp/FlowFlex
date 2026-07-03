---
domain: projects
module: templates
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Templates — Data Model

## `proj_templates`
`id, company_id (indexed, nullable for system), name, description, category, default_duration_days, is_system (boolean), deleted_at`.

## `proj_template_sections`
`id, template_id FK, company_id, name, order`.

## `proj_template_tasks`
`id, template_id FK, section_id FK, company_id, title, description, estimated_minutes, day_offset, order`.

## `proj_template_milestones`
`id, template_id FK, company_id, title, day_offset`.

> System templates: `company_id` null + `is_system = true`; read via a global-scope exception; never editable cross-tenant *(assumed)*.

## ERD

```mermaid
erDiagram
    proj_templates ||--o{ proj_template_sections : has
    proj_templates ||--o{ proj_template_milestones : has
    proj_template_sections ||--o{ proj_template_tasks : contains
    proj_templates {
        ulid id PK
        ulid company_id
        string name
        string category
        int default_duration_days
        boolean is_system
        timestamp deleted_at
    }
    proj_template_tasks {
        ulid id PK
        ulid template_id FK
        ulid section_id FK
        ulid company_id
        string title
        integer estimated_minutes (minutes, int — unit decision 2026-07-03)
        int day_offset
        int order
    }
    proj_template_milestones {
        ulid id PK
        ulid template_id FK
        ulid company_id
        string title
        int day_offset
    }
```
