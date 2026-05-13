---
type: module
domain: HR & People
panel: hr
module-key: hr.profiles
status: planned
color: "#4ADE80"
---

# Employee Profiles

> Core employee record management — every person from first day to last, with employment type, department, manager hierarchy, and status tracking.

**Panel:** `hr`
**Module key:** `hr.profiles`

## What It Does

Employee Profiles is the foundation of the entire HR domain. Every other HR module (leave, payroll, performance, scheduling) references the employee record created here. The module covers the full employee lifecycle: creating a record when someone is hired, updating it as their role evolves, and marking it terminated when they leave. Employment types, departments, job titles, manager hierarchy (self-referential), and emergency contacts are all managed here. Custom fields (JSON) allow companies to add their own attributes without schema changes.

## Features

### Core
- Employee CRUD: create on hire, edit on change, soft-terminate on exit (data retained)
- Employment types: `full_time`, `part_time`, `contractor`, `intern`
- Status: `active`, `inactive`, `on_leave`, `terminated`
- Department and job title fields — departments are a separate managed list
- Manager field: self-referential FK — forms the hierarchy used by the Org Chart module
- Emergency contact: name, relationship, phone — stored on employee record

### Advanced
- Hire date, probation end date, contract end date (for contractors)
- Document attachments: employment contract, ID copy, certifications — via file-storage module
- Custom fields: JSON column for company-specific attributes without migration
- Bulk actions: bulk department change, bulk status update from employee list
- Termination workflow: record reason, handover tasks, access revocation checklist — termination triggers `EmployeeTerminated` event consumed by payroll, leave, and IT modules

### AI-Powered
- Profile completeness score: nudge HR when mandatory fields are missing (e.g. emergency contact, contract, start date)
- Retention risk flag: AI scoring based on tenure, performance trends, and role changes — surfaced on employee detail page

## Data Model

```erDiagram
    employees {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string first_name
        string last_name
        string email
        string phone
        string employment_type
        string status
        string job_title
        ulid department_id FK
        ulid manager_id FK
        date hire_date
        date probation_end_date
        date contract_end_date
        date termination_date
        string termination_reason
        json emergency_contact
        json custom_fields
        timestamp deleted_at
        timestamps created_at/updated_at
    }

    departments {
        ulid id PK
        ulid company_id FK
        string name
        ulid manager_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `user_id` | FK to `users` — links HR record to login account |
| `manager_id` | Self-referential FK to `employees.id` |
| `custom_fields` | JSON — company-specific attributes |

## Permissions

- `hr.profiles.view`
- `hr.profiles.create`
- `hr.profiles.edit`
- `hr.profiles.terminate`
- `hr.profiles.view-sensitive`

## Filament

- **Resource:** `EmployeeResource` — table with search, filter by status/department, detail view with tabbed sections
- **Pages:** `ListEmployees`, `CreateEmployee`, `EditEmployee`, `ViewEmployee`
- **Custom pages:** None
- **Widgets:** `HeadcountWidget` (active employee count by department)
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| BambooHR | Employee records and lifecycle management |
| Workday HCM | Core HR employee master data |
| HiBob | People management and profiles |
| Personio | Employee data and HR administration |

## Related

- [[leave-management]]
- [[org-chart]]
- [[onboarding]]
- [[payroll]]
- [[performance-reviews]]
