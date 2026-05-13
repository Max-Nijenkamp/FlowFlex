---
type: module
domain: Field Service Management
panel: field
module-key: field.assets
status: planned
color: "#4ADE80"
---

# Customer Assets

> Customer-owned equipment register — asset details, installation history, service records, and warranty tracking.

**Panel:** `field`
**Module key:** `field.assets`

---

## What It Does

Customer Assets maintains a registry of equipment and installations managed by the field service team on behalf of their customers. Each asset record captures the asset type, model, serial number, installation date, warranty expiry, and location. Every work order completed against the asset is stored in its service history, giving technicians full context before they arrive on site. Warranty tracking alerts when an asset's warranty is approaching expiry, and service interval tracking triggers preventative maintenance work orders automatically.

---

## Features

### Core
- Asset register: customer, location, asset type, make, model, serial number, and installation date
- Asset status: active, decommissioned, under warranty, warranty expired
- Warranty tracking: warranty start and end dates with expiry alerts
- Service history: full list of work orders completed against the asset in chronological order
- Document attachments: attach manuals, commissioning certificates, and photos to the asset record
- QR code label: generate a QR code for each asset for fast mobile look-up on site

### Advanced
- Service intervals: define required maintenance intervals (e.g. annual service); auto-generate preventative work orders
- Asset hierarchy: group related assets (e.g. a boiler and its components) in a parent-child structure
- Replacement planning: flag assets approaching end of life for proactive replacement recommendations
- Multi-location assets: track assets that move between customer sites
- Custom asset fields: configure additional fields per asset type (e.g. pressure rating for HVAC units)
- Contract linking: link assets to maintenance contracts for entitlement checking

### AI-Powered
- Failure prediction: predict likely failure date based on asset age, usage, and historical fault frequency
- Maintenance schedule optimisation: recommend the optimal service visit schedule across all customer assets in a region
- Fault pattern detection: identify recurring fault types on the same asset model for proactive service bulletins

---

## Data Model

```erDiagram
    customer_assets {
        ulid id PK
        ulid company_id FK
        ulid customer_id FK
        ulid parent_asset_id FK
        string asset_type
        string make
        string model
        string serial_number
        string location_description
        date installation_date
        date warranty_start
        date warranty_end
        string status
        timestamps created_at_updated_at
    }

    asset_service_intervals {
        ulid id PK
        ulid company_id FK
        ulid asset_id FK
        string interval_name
        integer interval_months
        date last_serviced_date
        date next_due_date
        timestamps created_at_updated_at
    }

    customer_assets ||--o{ asset_service_intervals : "requires"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `customer_assets` | Asset register | `id`, `company_id`, `customer_id`, `asset_type`, `serial_number`, `warranty_end`, `status` |
| `asset_service_intervals` | Maintenance schedule per asset | `id`, `asset_id`, `interval_name`, `interval_months`, `next_due_date` |

---

## Permissions

```
field.assets.view
field.assets.create
field.assets.edit
field.assets.view-service-history
field.assets.manage-intervals
```

---

## Filament

- **Resource:** `App\Filament\Field\Resources\CustomerAssetResource`
- **Pages:** `ListCustomerAssets`, `CreateCustomerAsset`, `EditCustomerAsset`, `ViewCustomerAsset`
- **Custom pages:** `AssetServiceHistoryPage`, `AssetsMapPage`
- **Widgets:** `WarrantyExpiringWidget`, `AssetsOverdueServiceWidget`
- **Nav group:** Assets

---

## Displaces

| Feature | FlowFlex | ServiceTitan | FieldAware | Jobber |
|---|---|---|---|---|
| Asset register | Yes | Yes | Yes | Partial |
| Service history per asset | Yes | Yes | Yes | Yes |
| Warranty tracking | Yes | Yes | Partial | No |
| AI failure prediction | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[work-orders]] — work orders linked to assets; history viewable from asset record
- [[technician-dispatch]] — asset location shown on dispatch map
- [[service-level-agreements]] — asset type may determine applicable SLA
- [[job-invoicing]] — warranty status checked before billing labour to customer
