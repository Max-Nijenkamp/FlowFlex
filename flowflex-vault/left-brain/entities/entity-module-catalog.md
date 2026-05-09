---
type: entity
domain: Core Platform
table: module_catalog
primary_key: ulid
soft_deletes: false
last_updated: 2026-05-09
---

# Entity: Module Catalog

Master pricing table for all FlowFlex modules. Defines the per-user monthly price for each module key. Used by the billing engine to calculate monthly invoices.

**Table:** `module_catalog`  
**Multi-Tenant:** No — platform-level table, not scoped to company.

---

## Schema

```erDiagram
    module_catalog {
        ulid id PK
        string module_key
        string domain
        string name
        decimal per_user_monthly_price
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    module_catalog ||--o{ company_module_subscriptions : "priced by"
```

---

## Key Columns

| Column | Type | Notes |
|---|---|---|
| `module_key` | string | Unique. e.g. `hr.payroll`, `crm.pipeline` — matches `company_module_subscriptions.module_key` |
| `domain` | string | e.g. `hr`, `finance`, `crm` |
| `name` | string | Human-readable. e.g. "HR Payroll", "CRM Pipeline" |
| `per_user_monthly_price` | decimal(8,2) | Price in EUR per active user per month |
| `is_active` | boolean | Whether this module is currently available for activation |

---

## Billing Calculation

```php
// Monthly invoice for a company
$activeUsers = $company->users()->active()->count();

$monthlyTotal = ModuleCatalog::whereIn(
    'module_key',
    $company->moduleSubscriptions()->active()->pluck('module_key')
)->sum('per_user_monthly_price') * $activeUsers;
```

Invoice line items = one line per active module:
```
HR Payroll              × 15 users  × €2.50/user  = €37.50
CRM Pipeline            × 15 users  × €1.50/user  = €22.50
Finance Invoicing       × 15 users  × €2.00/user  = €30.00
───────────────────────────────────────────────────────────
Total                                               €90.00/month
```

---

## Price Setting

Prices are set by FlowFlex admin in the `/admin` panel and apply globally. Max can update a module's price — change takes effect on next billing cycle for all companies that have that module active.

---

## Business Rules

1. `module_key` must be unique — same key as used in `company_module_subscriptions`
2. Price changes take effect at start of next billing month — existing subscribers are not grandfathered (pricing is platform-wide). Note: at scale (100+ companies), Max should consider a `price_lock_until` column per `company_module_subscriptions` for commercial agreements
3. `is_active = false` hides module from activation UI (existing subscribers unaffected)
4. New modules start with `is_active = false` until ready for customers
5. Free modules (core, platform features) have `per_user_monthly_price = 0.00`

---

## Foundation Modules (price = 0.00)

Some platform-level modules are always free / included:

| Module Key | Name | Price |
|---|---|---|
| `core.auth` | Authentication & Identity | €0.00 |
| `core.notifications` | Notifications & Alerts | €0.00 |
| `core.audit-log` | Audit Log | €0.00 |
| `core.file-storage` | File Storage | €0.00 |
| `core.rbac` | Roles & Permissions | €0.00 |

All other modules have a non-zero per-user price set by Max.

---

## Related

- [[MOC_Entities]]
- [[entity-module-subscription]] — per-company activation records
- [[entity-company]] — active user count drives billing
- [[MOC_CorePlatform]] — Module Billing Engine consumes this table
