---
type: module
domain: IT & Security
domain-key: it
panel: it
module-key: it.assets
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [core.import, finance.assets, it.mdm]
fires-events: []
consumes-events: [EmployeeOffboarded]
patterns: [states, events]
tables: [it_assets, it_asset_assignments]
permission-prefix: it.assets
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Asset Inventory

Track IT hardware and software assets: laptops, phones, monitors, licences. Assign to employees, track lifecycle, and manage returns. The IT anchor — build first in `/it`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | assets assigned to employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, expiry alerts |
| Soft | [[domains/core/data-import\|core.import]] | bulk import |
| Soft | [[domains/finance/fixed-assets\|finance.assets]] | financial-asset link (`fin_asset_id`) |
| Soft | [[domains/it/mdm-integration\|it.mdm]] | device link |

---

## Core Features

- Asset record: name, type, serial number, asset tag, purchase date, warranty expiry, cost, status
- Asset types: laptop, desktop, phone, monitor, peripheral
- Status machine: `in_stock → assigned → in_repair → retired`
- Assign asset to an employee; assignment history per asset
- Warranty expiry tracking with alerts (30 days, once)
- Asset condition tracking on return (condition note)
- Bulk import via Core Data Import
- `EmployeeOffboarded` → assigned assets flagged for return + IT notified
- Financial link to `finance.assets` when active

---

## Data Model

### it_assets

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name / type | string | type in set |
| serial_number | string nullable | unique per company where set |
| asset_tag | string | unique per company |
| status | string default `in_stock` | state machine |
| assigned_to_employee_id | ulid nullable | current holder |
| purchase_date / warranty_expiry | date nullable | |
| cost_cents | bigint nullable | |
| fin_asset_id | ulid nullable | finance link |
| return_flagged_at | timestamp nullable | offboarding |
| warranty_alerted | boolean default false | once-guard |
| deleted_at | timestamp nullable | |

### it_asset_assignments — id, asset_id FK, company_id, employee_id FK, assigned_at, returned_at nullable, condition_note nullable

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `in_stock` | `assigned` | `it.assets.assign` | assignment row |
| `assigned` | `in_stock` (return) / `in_repair` | return action / repair | returned_at + condition note |
| `in_repair` | `assigned` / `in_stock` / `retired` | | |
| `in_stock` / `in_repair` | `retired` | `it.assets.retire` | finance disposal hint when linked *(assumed: note only)* |

---

## DTOs

### CreateAssetData — name, type (in set), asset_tag (unique per company), serial_number?, purchase_date?, warranty_expiry?, cost_cents?
### AssignAssetData — asset_id (in_stock), employee_id (active)
### ReturnAssetData — asset_id (assigned), condition_note?

## Services & Actions

- `AssignAssetAction` / `ReturnAssetAction` / `RetireAssetAction`
- Listener `FlagAssetsForReturnListener` on `EmployeeOffboarded` — flags assigned assets + notifies IT (queued, WithCompanyContext, per [[architecture/event-bus]])
- `WarrantyAlertCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `WarrantyAlertCommand` | notifications | daily | `warranty_alerted` once-guard |

---

## Filament

**Nav group:** Assets

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `AssetResource` | #1 CRUD resource | filters type/status/assignee; assign/return/retire actions; assignment history relation |
| `AssetExpiryWidget` | #6 widget | warranties expiring 30d |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('it.assets.view-any') && BillingService::hasModule('it.assets')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Note that bulk asset import inherits/uses a rate limiter (per architecture/security.md) on the import endpoint/action.

---

## Permissions

`it.assets.view-any` · `it.assets.manage` · `it.assets.assign` · `it.assets.retire`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Assign only from in_stock; return records condition + history
- [ ] `EmployeeOffboarded` flags that employee's assets + notifies
- [ ] Warranty alert once at 30d
- [ ] Duplicate asset_tag rejected
- [ ] Retire blocked while assigned

---

## Build Manifest

```
database/migrations/xxxx_create_it_assets_table.php
database/migrations/xxxx_create_it_asset_assignments_table.php
app/Models/IT/{Asset,AssetAssignment}.php
app/States/IT/Asset/{AssetState,InStock,Assigned,InRepair,Retired}.php
app/Data/IT/{CreateAssetData,AssignAssetData,ReturnAssetData}.php
app/Actions/IT/{AssignAssetAction,ReturnAssetAction,RetireAssetAction}.php
app/Listeners/IT/FlagAssetsForReturnListener.php
app/Console/Commands/IT/WarrantyAlertCommand.php
app/Providers/IT/ItServiceProvider.php
app/Filament/IT/Resources/AssetResource.php
app/Filament/IT/Widgets/AssetExpiryWidget.php
database/factories/IT/{AssetFactory,AssetAssignmentFactory}.php
tests/Feature/IT/{AssetLifecycleTest,OffboardingReturnTest}.php
```

---

## Related

- [[domains/it/access-provisioning]]
- [[domains/hr/employee-profiles]]
- [[domains/finance/fixed-assets]]
- [[architecture/event-bus]]
