---
domain: legal
module: compliance-registers
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Compliance Registers — Data Model

## legal_frameworks

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | GDPR / ISO 27001 / SOC 2 / custom |
| description | text nullable | |
| deleted_at | timestamp nullable | |

---

## legal_controls

| Column | Type | Notes |
|---|---|---|
| id, framework_id FK, company_id (indexed) | ulid | |
| reference | string | e.g. `A.5.1`; unique `(framework_id, reference)` |
| requirement | text | |
| status | string default `non-compliant` | compliant / partial / non-compliant / not-applicable |
| owner_id | ulid nullable FK users | |
| evidence_note | text nullable | required for compliant/partial *(assumed)* |
| policy_id | ulid nullable | legal.policies link |
| deleted_at | timestamp nullable | |

---

## legal_compliance_tasks

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), control_id FK | ulid | |
| title | string | |
| due_date | date | |
| frequency | string nullable | once / monthly / quarterly / annual |
| status | string | open / done |
| assignee_id | ulid FK users | |
| reminded | boolean default false | reminder once-guard |

---

## ERD

```mermaid
erDiagram
    legal_frameworks {
        ulid id PK
        ulid company_id FK
        string name
        text description
    }
    legal_controls {
        ulid id PK
        ulid framework_id FK
        ulid company_id FK
        string reference
        text requirement
        string status
        ulid owner_id FK
        text evidence_note
        ulid policy_id
    }
    legal_compliance_tasks {
        ulid id PK
        ulid company_id FK
        ulid control_id FK
        string title
        date due_date
        string frequency
        string status
        ulid assignee_id FK
        boolean reminded
    }
    legal_frameworks ||--o{ legal_controls : "controls"
    legal_controls ||--o{ legal_compliance_tasks : "tasks"
```

`policy_id` references `legal_policies` (owned by [[../policy-library/_module|legal.policies]]) — read-only.
