---
domain: hr
module: onboarding
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Onboarding — Data Model

Tables: `hr_onboarding_templates`, `hr_onboarding_tasks`, `hr_onboarding_plans`, `hr_onboarding_plan_tasks`. All `BelongsToCompany`. See [[../../../infrastructure/database]] and [[../../../security/tenancy-isolation]].

## hr_onboarding_templates

| Column | Type | Constraints |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | not null |
| description | text | nullable |
| department_id | ulid | nullable FK — null = company default |
| is_default | boolean | default false — one default per company *(assumed)* |
| deleted_at | timestamp | nullable |

## hr_onboarding_tasks

| Column | Type | Notes |
|---|---|---|
| id, template_id FK, company_id | ulid | |
| title | string | |
| description | text nullable | |
| assigned_role | string | hr / it / manager / employee |
| due_days_after_start | int nullable | *(assumed — relative due dates)* |
| order | int | |

## hr_onboarding_plans

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK, template_id FK | ulid | |
| started_at | timestamp | |
| completed_at | timestamp nullable | set when all tasks done/skipped |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, completed_at)`

## hr_onboarding_plan_tasks

| Column | Type | Notes |
|---|---|---|
| id, plan_id FK, task_id FK, company_id | ulid | |
| status | string default `pending` | pending / complete / skipped |
| completed_by | ulid nullable FK users | |
| completed_at | timestamp nullable | |

## ERD

```mermaid
erDiagram
    hr_onboarding_templates ||--o{ hr_onboarding_tasks : has
    hr_onboarding_templates ||--o{ hr_onboarding_plans : instantiated_as
    hr_onboarding_plans ||--o{ hr_onboarding_plan_tasks : contains
    hr_onboarding_tasks ||--o{ hr_onboarding_plan_tasks : materialized_into
    hr_onboarding_templates {
        ulid id
        ulid company_id
        string name
        text description
        ulid department_id
        boolean is_default
    }
    hr_onboarding_tasks {
        ulid id
        ulid template_id
        ulid company_id
        string title
        string assigned_role
        int due_days_after_start
        int order
    }
    hr_onboarding_plans {
        ulid id
        ulid company_id
        ulid employee_id
        ulid template_id
        timestamp started_at
        timestamp completed_at
    }
    hr_onboarding_plan_tasks {
        ulid id
        ulid plan_id
        ulid task_id
        ulid company_id
        string status
        ulid completed_by
        timestamp completed_at
    }
```
