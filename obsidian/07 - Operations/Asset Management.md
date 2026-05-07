---
tags: [flowflex, domain/operations, assets, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Asset Management

Track every physical asset — where it is, who has it, its condition and lifecycle stage. Full check-out/check-in with digital sign-off.

**Who uses it:** Operations managers, IT team, HR
**Filament Panel:** `operations`
**Depends on:** Core
**Phase:** 4
**Build complexity:** Medium — 2 resources, 1 page, 5 tables

---

## Features

- **Asset register** — every physical asset with make, model, serial number, purchase date, cost, category
- **Assignment to employee or location** — single source of truth for who has what
- **QR code asset labels** — print labels, scan on mobile to view and update the asset record
- **Lifecycle stages** — `in_use` / `in_storage` / `under_maintenance` / `disposed`
- **Check-out / check-in workflow** — formal handover logged with timestamp; optional signature from recipient
- **Maintenance link** — flagging an asset "under maintenance" creates a work order in [[Equipment Maintenance]]
- **IT asset tracking cross-link** — linked to [[IT Asset Management]] for software licence and device tracking
- **Disposal recording** — write-off or sale; fires `AssetDisposed` event; feeds to [[Fixed Asset & Depreciation]] for accounting
- **Asset history timeline** — full log of every check-out, check-in, maintenance event, and lifecycle change
- **Bulk import** — CSV import for initial asset loading
- **Low-value vs capitalised assets** — flag whether asset should appear in fixed asset register

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `asset_categories`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Laptop", "Vehicle", "Tool" |
| `description` | text nullable | |
| `is_capitalised` | boolean | appears in fixed asset register |

### `assets`
| Column | Type | Notes |
|---|---|---|
| `asset_category_id` | ulid FK | → asset_categories |
| `name` | string | e.g. "MacBook Pro 16" — Max" |
| `make` | string nullable | |
| `model` | string nullable | |
| `serial_number` | string nullable | |
| `qr_code` | string unique nullable | UUID printed on label |
| `purchase_date` | date nullable | |
| `purchase_cost` | decimal(10,2) nullable | |
| `status` | enum | `in_use`, `in_storage`, `under_maintenance`, `disposed` |
| `assigned_tenant_id` | ulid FK nullable | → tenants (current holder) |
| `assigned_location` | string nullable | room/site if not person-assigned |
| `condition` | enum | `excellent`, `good`, `fair`, `poor` |
| `notes` | text nullable | |
| `disposed_at` | timestamp nullable | |
| `disposal_value` | decimal(10,2) nullable | |
| `disposal_notes` | text nullable | |

### `asset_assignments`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK | → assets |
| `tenant_id` | ulid FK nullable | who received it |
| `location` | string nullable | if location-based |
| `checked_out_at` | timestamp | |
| `checked_out_by_tenant_id` | ulid FK | who processed check-out |
| `checked_in_at` | timestamp nullable | |
| `checked_in_by_tenant_id` | ulid FK nullable | |
| `recipient_signature_file_id` | ulid FK nullable | → files |
| `notes` | text nullable | |

### `asset_maintenance_logs`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK | → assets |
| `work_order_id` | ulid FK nullable | → work_orders in [[Equipment Maintenance]] |
| `description` | text | |
| `performed_at` | date | |
| `cost` | decimal(10,2) nullable | |
| `performed_by` | string nullable | internal or external |

### `asset_qr_labels`
| Column | Type | Notes |
|---|---|---|
| `asset_id` | ulid FK | → assets |
| `generated_at` | timestamp | |
| `generated_by_tenant_id` | ulid FK | |
| `file_id` | ulid FK nullable | → files (PDF label sheet) |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `AssetAssigned` | `asset_id`, `tenant_id` | Notification to recipient |
| `AssetDisposed` | `asset_id`, `disposal_value` | [[Fixed Asset & Depreciation]] (write-off entry) |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeOffboarded` | [[Offboarding]] | Creates asset recall task for all assets assigned to departing employee |
| `EmployeeHired` | [[Recruitment & ATS]] | Triggers equipment request task in onboarding checklist |

---

## Permissions

```
operations.assets.view
operations.assets.create
operations.assets.edit
operations.assets.delete
operations.assets.checkout
operations.assets.checkin
operations.assets.dispose
operations.asset-categories.view
operations.asset-categories.create
operations.asset-categories.edit
operations.asset-categories.delete
```

---

## Related

- [[Operations Overview]]
- [[IT Asset Management]]
- [[Fixed Asset & Depreciation]]
- [[Equipment Maintenance]]
- [[Offboarding]]
