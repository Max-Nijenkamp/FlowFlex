---
type: product
category: pricing
color: "#38BDF8"
---

# Pricing Model

---

## Core Principle: Per-User, Per-Module

FlowFlex has no fixed pricing tiers. There is no Starter, Growth, or Enterprise plan. Instead, a company pays for each module they have activated, multiplied by the number of active users in their workspace.

```
Monthly invoice = sum(module_price_per_user) × active_user_count
```

**Example**: A company with 15 active users that has activated HR Payroll (€2.50/user), CRM Pipeline (€1.50/user), and Finance Invoicing (€2.00/user) pays:

```
HR Payroll              × 15 users  × €2.50/user  = €37.50
CRM Pipeline            × 15 users  × €1.50/user  = €22.50
Finance Invoicing       × 15 users  × €2.00/user  = €30.00
─────────────────────────────────────────────────────────
Total                                               €90.00/month
```

This model scales linearly with both company size and product adoption. A company that grows from 15 to 30 users doubles its bill without activating anything new. A company that activates three more modules doubles its bill without adding a single user.

---

## Always-On Core Modules (Price: €0.00)

Some platform-level capabilities are included with every FlowFlex workspace and are never gated behind a subscription:

| Module Key | Name | Description |
|---|---|---|
| `core.auth` | Authentication & Identity | Login, 2FA, session management, password reset |
| `core.notifications` | Notifications & Alerts | In-app notifications, email alerts, notification preferences |
| `core.audit-log` | Audit Log | Full activity trail across all domains |
| `core.file-storage` | File Storage | Upload and store files attached to any record |
| `core.rbac` | Roles & Permissions | Role creation, permission assignment, team-scoped access |

These modules are always active, always free, and cannot be deactivated. Every company subscription starts with these capabilities in place.

Additionally, the company settings panel (`/app`), the setup wizard, and the module marketplace are always accessible — a company must always be able to manage its own workspace.

---

## Module Marketplace

All paid modules are activated through the Module Marketplace, accessible from the `/app` panel by users with the `owner` or `admin` role.

Activation is a one-click action. The module is available to all users immediately after activation. Deactivation gates access but does not delete data — a company can reactivate a module and return to the same state.

The marketplace displays each available module with:
- Module name and domain
- Description and included features
- Per-user monthly price
- Activation status (active / inactive)

Pricing is set by FlowFlex admins in the `/admin` panel and applies globally to all companies. Price changes take effect at the start of the next billing month.

---

## Data Model

Two tables drive the entire pricing and access system:

**`module_catalog`** — platform-level table listing all available modules. Columns: `module_key` (unique, e.g. `hr.payroll`), `domain`, `name`, `per_user_monthly_price`, `is_active`. Not scoped to any company.

**`company_module_subscriptions`** — per-company activation records. Columns: `company_id`, `module_key`, `activated_at`, `deactivated_at`, `activated_by`. One row per module per company.

`BillingService::hasModule(string $key)` queries `company_module_subscriptions` for the current company. This method is called in the `canAccess()` check on every gated Filament resource, making module gating automatic and consistent.

---

## Billing Subscription Status

The `companies` table carries the overall subscription status (`trial`, `active`, `suspended`, `cancelled`). This status is separate from individual module activations:

- `trial` — company is in the free trial period; all activated modules are accessible
- `active` — paid subscription; modules accessible per `company_module_subscriptions`
- `suspended` — payment failed; all panel access blocked by `SetCompanyContext` middleware until resolved
- `cancelled` — workspace archived; data retained per GDPR retention policy, login blocked

---

## GDPR and Data Rights

- Companies can export their full dataset at any time from the data portability section in company settings
- Cancellation does not immediately delete data — a 90-day retention window applies before scheduled purge
- GDPR erasure requests (DSARs) are processed through the Legal & Compliance domain and trigger anonymisation across all domains
- FlowFlex does not lock in data — portability is a baseline feature, not an enterprise add-on
