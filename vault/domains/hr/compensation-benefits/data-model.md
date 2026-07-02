---
domain: hr
module: compensation-benefits
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Data Model — Compensation & Benefits

Four tables (planned). All carry `company_id` for tenant isolation — see [[../../../security/tenancy-isolation]] and [[../../../infrastructure/database]].

## hr_compensation_bands

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| job_grade | string | unique `(company_id, job_grade, department_id)` |
| department_id | ulid nullable FK | null = company-wide |
| min_salary_cents / mid_salary_cents / max_salary_cents | bigint | min ≤ mid ≤ max |
| currency | string(3) | |
| deleted_at | timestamp nullable | |

## hr_benefits

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| type | string | insurance / pension / allowance |
| cost_per_month_cents | bigint | employee cost |
| employer_contribution_cents | bigint | |
| deleted_at | timestamp nullable | |

## hr_employee_benefits

| Column | Type | Notes |
|---|---|---|
| id, company_id, employee_id FK, benefit_id FK | ulid | unique active `(employee_id, benefit_id)` where `unenrolled_at` null |
| enrolled_at | timestamp | |
| unenrolled_at | timestamp nullable | |

## hr_salary_history

Append-only trail. No update/delete.

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK | ulid | |
| 🔐 amount_raw | text | encrypted integer cents (new salary) — see [[security]] |
| salary_band | string | derived coarse band |
| effective_date | date | |
| reason | string | hire / promotion / comp-review / correction |
| changed_by | ulid FK users | |
| created_at | timestamp | append-only — no update/delete |

## ERD

```mermaid
erDiagram
    hr_compensation_bands {
        ulid id
        ulid company_id
        string job_grade
        ulid department_id
        bigint min_salary_cents
        bigint mid_salary_cents
        bigint max_salary_cents
        string currency
    }
    hr_benefits {
        ulid id
        ulid company_id
        string name
        string type
        bigint cost_per_month_cents
        bigint employer_contribution_cents
    }
    hr_employee_benefits {
        ulid id
        ulid company_id
        ulid employee_id
        ulid benefit_id
        timestamp enrolled_at
        timestamp unenrolled_at
    }
    hr_salary_history {
        ulid id
        ulid company_id
        ulid employee_id
        text amount_raw
        string salary_band
        date effective_date
        string reason
        ulid changed_by
    }
    hr_benefits ||--o{ hr_employee_benefits : "enrolled as"
    hr_employee_benefits }o--|| EMPLOYEE : "for"
    hr_salary_history }o--|| EMPLOYEE : "records"
    hr_compensation_bands }o--o| DEPARTMENT : "scoped to"
```

Note: `EMPLOYEE` and `DEPARTMENT` are owned by [[../employee-profiles/_module]].

## Related

- [[../../../infrastructure/database]]
- [[../../../security/tenancy-isolation]]
- [[architecture]]
