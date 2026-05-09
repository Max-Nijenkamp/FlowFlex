---
type: entity
domain: Foundation
table: admins
primary_key: ulid
soft_deletes: true
last_updated: 2026-05-09
---

# Entity: Admin

FlowFlex internal staff user — Layer 1 RBAC. Completely separate from tenant `User` records. Authenticates at `/admin` Filament panel via the `admin` guard.

**Table:** `admins`  
**Multi-Tenant:** No — platform-level, no `company_id`.

---

## Schema

```erDiagram
    admins {
        ulid id PK
        string name
        string email
        string password
        string role
        timestamp last_login_at
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }
```

---

## Key Columns

| Column | Type | Notes |
|---|---|---|
| `id` | ULID | Primary key |
| `name` | string(255) | Display name |
| `email` | string(255) | Unique across all admins |
| `role` | enum | `super_admin`, `support`, `billing`, `developer` |
| `last_login_at` | timestamp | Audit / access tracking |

---

## Admin Roles

| Role | Description | Key Abilities |
|---|---|---|
| `super_admin` | Max + trusted FlowFlex founders | Full platform access, create other admins, delete tenants |
| `support` | Customer support agents | View tenant data, impersonate users (audit-logged), reset passwords |
| `billing` | Finance/billing team | View subscriptions, manage module pricing, handle refunds |
| `developer` | Engineering | System health, queue monitoring, Telescope, Pulse |

---

## Authentication

- Login URL: `/admin/login`
- Guard: `admin` (separate from `web` — no cross-contamination with tenant sessions)
- Model bound in `AdminPanelProvider::authModel(Admin::class)`
- No `company_id` — admins are not tenants

---

## Business Rules

1. Admin accounts created only by existing `super_admin` — no self-registration
2. `support` role impersonation is always audit-logged in Spatie Activitylog
3. Admin soft-deleted → immediate session revocation via `AdminObserver`
4. MFA (TOTP) required for all admin roles (enforced at panel level)
5. Admin cannot access `/app` Filament panel — separate guard prevents this

---

## Related

- [[MOC_Entities]]
- [[entity-user]] — tenant-side users (separate table, separate guard)
- [[auth-rbac]] — 2-layer RBAC explanation
- [[admin-panel-flowflex]] — what the admin panel contains
- [[MOC_Foundation]]
