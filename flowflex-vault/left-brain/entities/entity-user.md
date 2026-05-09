---
type: entity
domain: Core Platform
table: users
primary_key: ulid
soft_deletes: true
last_updated: 2026-05-08
---

# Entity: User

Platform user — the person who logs in to the Filament admin panels. One user belongs to one company.

**Table:** `users`  
**Multi-Tenant:** Yes — `company_id` on every row.

---

## Schema

```erDiagram
    users {
        ulid id PK
        ulid company_id FK
        string first_name
        string last_name
        string email
        string password
        string locale
        string timezone
        boolean two_factor_enabled
        timestamp email_verified_at
        timestamp last_login_at
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    companies ||--o{ users : "owns"
    users }o--o| employees : "may link to"
```

---

## Key Columns

| Column | Type | Notes |
|---|---|---|
| `company_id` | ULID FK | Tenant scope |
| `email` | string | Unique within company |
| `two_factor_enabled` | boolean | TOTP 2FA |
| `last_login_at` | timestamp | Audit / session security |

---

## Relationships

| Relationship | Type | Description |
|---|---|---|
| `company()` | belongsTo | Owning company |
| `employee()` | hasOne | Optional HR employee profile |
| `roles()` | belongsToMany | Via Spatie Permission (team-scoped) |
| `permissions()` | belongsToMany | Via Spatie Permission |

---

## Two-Layer RBAC

`users` table = company-side users (Layer 2).  
`admins` table = FlowFlex super-admins (Layer 1, separate model + guard).

See [[auth-rbac]] for full details.

---

## Business Rules

1. Email unique per company (same email OK in different companies)
2. Company owner role assigned at company creation — cannot be removed
3. Soft-deleted users lose all panel access immediately
4. User deletion triggers `UserDeactivated` event → IT domain revokes SSO

---

## Related

- [[MOC_Entities]]
- [[entity-company]]
- [[entity-employee]]
- [[auth-rbac]]
- [[concept-rbac]]
