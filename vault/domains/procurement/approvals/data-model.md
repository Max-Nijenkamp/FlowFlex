---
domain: procurement
module: approvals
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Approvals — Data Model

Owns `proc_approval_rules`, `proc_approval_delegations`. No approval *action* rows here (those live in each consumer module — [[../../../security/data-ownership]]).

## ERD

```mermaid
erDiagram
    proc_approval_rules {
        ulid id PK
        ulid company_id FK
        string applies_to "requisition|po"
        bigint min_amount_cents
        bigint max_amount_cents "null = infinity"
        string category "null = all"
        string approver_role "spatie role"
        int level "chain order"
        int escalation_days "default 3"
        bool is_active
    }
    proc_approval_delegations {
        ulid id PK
        ulid company_id FK
        ulid delegator_id FK
        ulid delegate_id FK "!= delegator"
        date start_date
        date end_date "&gt;= start"
    }
    proc_approval_rules ||--o{ proc_approval_delegations : "resolved-against at act time"
```

## proc_approval_rules

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| applies_to | string | requisition / po |
| min_amount_cents / max_amount_cents | bigint | max nullable = ∞; ranges must not overlap per (applies_to, category, level) |
| category | string nullable | null = all |
| approver_role | string | spatie role name (or user_id *(assumed: role-based v1)*) |
| level | int | chain order |
| escalation_days | int default 3 | |
| is_active | boolean | |

## proc_approval_delegations

id, company_id (indexed), delegator_id FK, delegate_id FK (≠ delegator), start_date/end_date (end ≥ start). Overlapping delegations per delegator rejected.

## Integrity rules

- Amount ranges may not overlap within the same `(applies_to, category, level)`.
- Every table row carries `company_id`; all queries run under CompanyScope.

## Related

- [[_module]] · [[architecture]] · [[api]] · [[../../../security/data-ownership]]
