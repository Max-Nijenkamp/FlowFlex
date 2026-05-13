---
type: module
domain: IT & Security
panel: it
module-key: it.assets
status: planned
color: "#4ADE80"
---

# Asset Management

> Hardware and software asset inventory with assignment tracking, lifecycle management, and depreciation calculation.

**Panel:** `it`
**Module key:** `it.assets`

## What It Does

Asset Management is the authoritative register of every hardware and software asset the company owns or leases. Each asset record tracks the item from procurement through assignment, maintenance, and disposal. Assets are linked to the employee they are assigned to, so IT always knows who has what device. Straight-line depreciation is calculated automatically from purchase cost and expected useful life. Integration with the service desk means assets can be linked directly to support tickets.

## Features

### Core
- Asset types: laptop, desktop, monitor, phone, tablet, server, network device, printer, software licence, SaaS subscription
- Asset record: make, model, serial number, purchase date, purchase cost, vendor, warranty expiry, assigned employee
- Asset statuses: in stock, assigned, under repair, retired, disposed
- Assignment workflow: assign to employee with start date; history of past assignments per asset
- QR code generation: print QR code label for any asset; scan to open record on mobile
- Warranty and lease expiry alerts: notification before warranty or lease end date

### Advanced
- Depreciation: straight-line depreciation from purchase cost over configurable useful life (years); current book value calculated automatically
- Asset categories and custom fields: extend asset records with category-specific fields (e.g., RAM and storage for laptops)
- Bulk import: CSV import for migrating existing asset register
- Asset audit: periodic verification scan — IT confirms asset is still present and assigned correctly; flag discrepancies
- Consumables tracking: track consumable stock (printer cartridges, cables) with reorder alerts
- Disposal workflow: record disposal method (trade-in, recycling, internal transfer), date, and recovered value

### AI-Powered
- Refresh cycle prediction: flag assets approaching end of useful life based on age and category refresh norms
- Procurement cost comparison: when a refresh is due, compare current market price vs original purchase cost

## Data Model

```erDiagram
    it_assets {
        ulid id PK
        ulid company_id FK
        string asset_type
        string category
        string make
        string model
        string serial_number
        decimal purchase_cost
        date purchase_date
        integer useful_life_years
        decimal current_book_value
        date warranty_expiry
        string status
        ulid assigned_to FK
        date assigned_on
        timestamps timestamps
        softDeletes deleted_at
    }

    it_asset_assignments {
        ulid id PK
        ulid asset_id FK
        ulid employee_id FK
        date assigned_on
        date returned_on
        string notes
    }

    it_asset_audit_records {
        ulid id PK
        ulid asset_id FK
        ulid audited_by FK
        date audited_on
        string result
        string notes
    }

    it_assets ||--o{ it_asset_assignments : "assigned via"
    it_assets ||--o{ it_asset_audit_records : "audited by"
```

| Table | Purpose |
|---|---|
| `it_assets` | Asset master records with current status and value |
| `it_asset_assignments` | History of employee assignments |
| `it_asset_audit_records` | Physical audit confirmation records |

## Permissions

```
it.assets.view-any
it.assets.create
it.assets.assign
it.assets.update
it.assets.dispose
```

## Filament

**Resource class:** `AssetResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `AssetAuditPage` (bulk confirmation workflow for periodic audits)
**Widgets:** `AssetExpiryWidget` (warranties and leases expiring in 60 days), `AssetStatusSummaryWidget`
**Nav group:** Assets

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Snipe-IT | Open-source IT asset management |
| Asset Panda | Asset tracking and assignment |
| Freshservice Assets | ITSM-integrated asset management |
| Lansweeper | Network asset discovery and inventory |

## Related

- [[software-licenses]] — software assets link to licence records
- [[service-desk]] — tickets can be linked to an asset in fault
- [[audit-compliance]] — asset register used in IT audit evidence
- [[capacity-planning]] — hardware asset data feeds infrastructure capacity metrics
- [[../hr/INDEX]] — employees linked to asset assignments
- [[../finance/INDEX]] — asset depreciation feeds balance sheet
