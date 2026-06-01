---
type: module
domain: IT & Security
panel: it
module-key: it.assets
status: planned
color: "#4ADE80"
---

# Asset Inventory

Track IT hardware and software assets: laptops, phones, monitors, licences. Assign to employees, track lifecycle, and manage returns.

## Core Features

- Asset record: name, type, serial number, asset tag, purchase date, warranty expiry, cost, status
- Asset types: laptop, desktop, phone, monitor, peripheral, software licence
- Status machine: `in_stock → assigned → in_repair → retired`
- Assign asset to an employee (links to HR)
- Assignment history per asset
- Warranty/lease expiry tracking with alerts
- Asset condition tracking
- Bulk import via Core Data Import
- Depreciation tracking (links to Finance fixed assets if active)

## Data Model

| Table | Key Columns |
|---|---|
| `it_assets` | company_id, name, type, serial_number, asset_tag, status, assigned_to_employee_id, purchase_date, warranty_expiry, cost_cents |
| `it_asset_assignments` | asset_id, company_id, employee_id, assigned_at, returned_at, condition_note |

## Filament

**Nav group:** Assets

- `AssetResource` — list (filter by type/status/assignee), create, assign action, view
- Assignment history on view page
- `AssetExpiryWidget` — warranties expiring soon

## Cross-Domain / Events

- Consumes `EmployeeOffboarded` → flag assigned assets for return

## Related

- [[domains/it/access-provisioning]]
- [[domains/hr/employee-profiles]]
- [[domains/finance/fixed-assets]]
