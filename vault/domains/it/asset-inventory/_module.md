---
domain: it
module: asset-inventory
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Asset Inventory

Track IT hardware and software assets (laptops, phones, monitors, licences): assign to employees, track lifecycle, and manage returns. The IT anchor — build first in `/it`. Owns `it_assets` + `it_asset_assignments`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | assets assigned to employees |
| Hard | core.billing + core.rbac + core.notifications | gating + permissions + expiry alerts |
| Soft | core.import | bulk asset import |
| Soft | [[../../finance/fixed-assets/_module\|finance.assets]] | financial-asset link (`fin_asset_id`) |
| Soft | [[../mdm-integration/_module\|it.mdm]] | device link |

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Assign only from in_stock; return records condition + history
- [ ] `EmployeeOffboarded` flags that employee's assets + notifies
- [ ] Warranty alert once at 30d
- [ ] Duplicate asset_tag rejected
- [ ] Retire blocked while assigned

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `EmployeeOffboarded` | hr.profiles | `FlagAssetsForReturnListener` (this module's OWN listener) sets `return_flagged_at` on this module's OWN `it_assets` + notifies IT (queued, WithCompanyContext) |
| Reads | employee lookup | hr.profiles | assignee validation on assign (active employee only) |
| Soft link | `fin_asset_id` | finance.assets | financial-asset reference stored on `it_assets` when finance.assets active |

**Data ownership:** `it.assets` writes only `it_assets` + `it_asset_assignments`; all cross-domain effects go through events / owning-service reads, never another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture|asset-inventory.architecture]]
- [[data-model|asset-inventory.data-model]]
- [[security|asset-inventory.security]]
- [[decisions|asset-inventory.decisions]]
- [[unknowns|asset-inventory.unknowns]]
- [[features/asset-record|asset-record feature]]
- [[features/assignment-return|assignment-return feature]]
- [[features/offboarding-return-flags|offboarding-return-flags feature]]
- [[features/warranty-alerts|warranty-alerts feature]]
- [[../../hr/employee-profiles/_module|hr.profiles]]
- [[../../finance/fixed-assets/_module|finance.assets]]
- [[../../../architecture/event-bus]]
