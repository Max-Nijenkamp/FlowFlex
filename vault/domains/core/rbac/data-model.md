---
domain: core
module: rbac
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# RBAC — Data Model

No custom tables. Uses the four `spatie/laravel-permission` tables, all scoped by `team_id = company_id`.

| Table | Purpose |
|---|---|
| `permissions` | all permission strings, scoped by `team_id` |
| `roles` | named roles per company team |
| `model_has_roles` | user → role assignments (polymorphic `model_type`/`model_id`) |
| `role_has_permissions` | role → permission assignments |

## ERD

```mermaid
erDiagram
    USERS ||--o{ MODEL_HAS_ROLES : assigned
    ROLES ||--o{ MODEL_HAS_ROLES : grants
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : has
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : in

    USERS {
        ulid id PK
        ulid company_id
        string name
        string email
    }
    ROLES {
        bigint id PK
        ulid team_id "= company_id"
        string name
        string guard_name
    }
    PERMISSIONS {
        bigint id PK
        ulid team_id "= company_id"
        string name "domain.module.action"
        string guard_name
    }
    MODEL_HAS_ROLES {
        bigint role_id FK
        string model_type
        ulid model_id "user id"
        ulid team_id
    }
    ROLE_HAS_PERMISSIONS {
        bigint permission_id FK
        bigint role_id FK
    }
```

## Related

- [[_module]] · [[security]] · [[../../../architecture/caching]]
