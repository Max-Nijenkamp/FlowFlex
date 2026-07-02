---
domain: hr
module: shift-scheduling
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shift Scheduling — Data Model

Two tenant-scoped tables (planned). See [[../../../security/tenancy-isolation]] and [[../../../infrastructure/database]].

## hr_shifts

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | pk | |
| company_id | ulid | indexed | tenant scope |
| employee_id | ulid | nullable FK | null = unassigned (coverage gap) |
| date | date | not null | |
| start_time | time | not null | |
| end_time | time | end after start | overnight: `end_next_day` flag *(assumed)* |
| role | string | not null | position label |
| status | string | default `draft` | draft / published / cancelled |
| deleted_at | timestamp | nullable | soft delete |

**Indexes:** `(company_id, date, status)`, `(company_id, employee_id, date)`

## hr_shift_swap_requests

| Column | Type | Notes |
|---|---|---|
| id | ulid | pk |
| company_id | ulid | indexed, tenant scope |
| requester_id | ulid FK hr_employees | |
| recipient_id | ulid FK hr_employees | |
| shift_id | ulid FK hr_shifts | |
| status | string default `pending` | pending / accepted / approved / declined |
| manager_approved_at | timestamp nullable | set on final approval |

## ERD

```mermaid
erDiagram
    hr_employees ||--o{ hr_shifts : "assigned to"
    hr_employees ||--o{ hr_shift_swap_requests : requester
    hr_employees ||--o{ hr_shift_swap_requests : recipient
    hr_shifts ||--o{ hr_shift_swap_requests : "swap of"

    hr_shifts {
        ulid id PK
        ulid company_id
        ulid employee_id FK "nullable"
        date date
        time start_time
        time end_time
        string role
        string status "draft/published/cancelled"
        timestamp deleted_at
    }
    hr_shift_swap_requests {
        ulid id PK
        ulid company_id
        ulid requester_id FK
        ulid recipient_id FK
        ulid shift_id FK
        string status "pending/accepted/approved/declined"
        timestamp manager_approved_at
    }
```

## Related

- [[api]] · [[architecture]] · [[security]]
- [[../../../infrastructure/database]]
