---
tags: [flowflex, domain/it, saas, spend, shadow-it, phase/6]
domain: IT & Security
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# SaaS Spend Management

Discover what tools the company is actually using and stop paying for things nobody uses. Track every SaaS subscription, flag shadow IT, and get renewal alerts before auto-renew charges hit.

**Who uses it:** IT team, finance team
**Filament Panel:** `it`
**Depends on:** Core
**Phase:** 6
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **SaaS subscription register** — log every SaaS tool with vendor, category, monthly cost, billing cycle, renewal date, and seat count
- **Seat vs tenant tracking** — record which tenants use each subscription in `assigned_tenants` JSON; spot underutilised licences
- **Shadow IT tracking** — mark subscriptions as `is_shadow_it` when discovered via SSO log or expense report review; review and approve or block
- **Shadow IT discovery** — `shadow_it_discoveries` table captures tool name, URL, first-seen date, and how it was detected; status workflow: reviewing → approved / blocked
- **Monthly spend per tool** — `saas_spend_records` logs actual charges per subscription per period; total monthly spend dashboard widget
- **Renewal calendar** — calendar view of all upcoming renewals; filterable by month; click to see subscription detail
- **`SaaSLicenceExpiring` alert** — event fired when `renewal_date` is within configurable threshold; notifies IT and finance teams
- **Licence optimisation** — surface subscriptions where `assigned_tenants` count is significantly below `seats` purchased
- **Category tagging** — tag subscriptions by category (productivity/security/design/CRM/marketing/finance/etc.); total spend by category chart
- **Export** — export full SaaS register as CSV for finance reconciliation or vendor negotiations

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `saas_subscriptions`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Figma" |
| `vendor` | string nullable | |
| `category` | string nullable | e.g. "design", "security" |
| `monthly_cost` | decimal(10,2) nullable | |
| `billing_cycle` | enum | `monthly`, `annual`, `quarterly` |
| `renewal_date` | date nullable | |
| `seats` | integer nullable | licensed seats |
| `assigned_tenants` | json nullable | array of tenant IDs |
| `status` | enum | `active`, `cancelled`, `expired` |
| `is_shadow_it` | boolean default false | |
| `discovered_via` | string nullable | e.g. "expense report", "SSO log" |
| `notes` | text nullable | |

### `saas_spend_records`
| Column | Type | Notes |
|---|---|---|
| `saas_subscription_id` | ulid FK | → saas_subscriptions |
| `amount` | decimal(10,2) | |
| `currency` | string(3) default 'GBP' | |
| `period_start` | date | |
| `period_end` | date | |
| `invoice_ref` | string nullable | |

### `shadow_it_discoveries`
| Column | Type | Notes |
|---|---|---|
| `name` | string | tool name |
| `url` | string nullable | |
| `detected_via` | string | e.g. "expense review", "SSO log" |
| `first_seen_at` | timestamp | |
| `status` | enum | `reviewing`, `approved`, `blocked` |
| `reviewed_by` | ulid FK nullable | → tenants |
| `reviewed_at` | timestamp nullable | |
| `notes` | text nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `SaaSLicenceExpiring` | `saas_subscription_id`, `renewal_date` | Notification to IT team and finance team |

---

## Events Consumed

None — SaaS Spend is manually maintained or populated from expense import.

---

## Permissions

```
it.saas-subscriptions.view
it.saas-subscriptions.create
it.saas-subscriptions.edit
it.saas-subscriptions.delete
it.saas-spend-records.view
it.saas-spend-records.create
it.shadow-it-discoveries.view
it.shadow-it-discoveries.review
```

---

## Related

- [[IT Overview]]
- [[IT Asset Management]]
- [[Budgeting & Forecasting]]
- [[Finance Overview]]
