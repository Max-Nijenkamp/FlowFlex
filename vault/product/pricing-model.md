---
type: product
category: pricing
color: "#38BDF8"
---

# Pricing Model

---

## Core Principle: Per-User, Per-Module

No fixed tiers. No Starter / Growth / Enterprise plans.

```
Monthly invoice = sum(module_price_per_user) × active_user_count
```

**Example** — 15 active users, 3 modules:

```
HR Payroll        × 15 users × €2.50/user = €37.50
CRM Pipeline      × 15 users × €1.50/user = €22.50
Finance Invoicing × 15 users × €2.00/user = €30.00
──────────────────────────────────────────────────
Total                                       €90.00/month
```

---

## Always-On Core (Free)

| Module Key | Name | Description |
|---|---|---|
| `core.auth` | Authentication & Identity | Login, 2FA, sessions, password reset |
| `core.notifications` | Notifications | In-app alerts, email notifications |
| `core.audit-log` | Audit Log | Full activity trail across all domains |
| `core.file-storage` | File Storage | File uploads on any record |
| `core.rbac` | Roles & Permissions | Role creation, permission assignment |

Always active, always free, cannot be deactivated. Company settings panel (`/app`), setup wizard, and module marketplace always accessible.

---

## Module Marketplace

All paid modules activated through the marketplace at `/app`. Owner or admin role required.

Activation is one-click. Available immediately. Deactivation gates access but does not delete data — reactivation restores the same state.

Module prices are set by FlowFlex staff in `/admin`. Price changes take effect at start of next billing month.

---

## Data Model

**`module_catalog`** — platform-level, not scoped to company.

| Column | Type | Notes |
|---|---|---|
| `module_key` | string (unique) | e.g. `hr.payroll` |
| `domain` | string | e.g. `hr` |
| `name` | string | Display name |
| `per_user_monthly_price` | decimal | EUR |
| `is_active` | boolean | Listed in marketplace |

**`company_module_subscriptions`** — per-company activation records.

| Column | Type | Notes |
|---|---|---|
| `company_id` | ulid | FK to companies |
| `module_key` | string | FK to module_catalog |
| `activated_at` | timestamp | |
| `deactivated_at` | timestamp nullable | |
| `activated_by` | ulid | FK to users |

`BillingService::hasModule(string $key)` queries this table for the current company. Called in `canAccess()` on every gated Filament resource.

---

## Company Subscription Status

Stored on `companies.subscription_status`:

| Status | Meaning |
|---|---|
| `trial` | Free trial — all activated modules accessible |
| `active` | Paid — modules accessible per `company_module_subscriptions` |
| `suspended` | Payment failed — all panel access blocked by middleware |
| `cancelled` | Workspace archived — data retained per GDPR retention policy |

---

## GDPR and Data Rights

- Full dataset export available at any time from company settings
- Cancellation triggers 90-day retention window before purge (not immediate deletion)
- GDPR erasure requests (DSARs) processed via Legal domain — anonymises across all domains
- Data portability is a baseline feature, not an enterprise add-on
