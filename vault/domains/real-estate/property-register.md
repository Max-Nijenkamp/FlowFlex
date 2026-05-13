---
type: module
domain: Real Estate & Property
panel: realestate
module-key: realestate.properties
status: planned
color: "#4ADE80"
---

# Property Register

> Central property register — address, type, size, ownership structure, current valuation, and status.

**Panel:** `realestate`
**Module key:** `realestate.properties`

---

## What It Does

Property Register is the master data record for every property in the portfolio. Each property record captures the physical details (address, type, gross and net lettable area, year built), the ownership structure (freehold, leasehold, fund-owned), the current market valuation with date, and the portfolio status (available, let, under development, for sale). All other modules — leases, maintenance, billing — reference property records as their anchor. Document storage allows title deeds, planning permissions, and surveys to be attached directly to the property.

---

## Features

### Core
- Property record creation: address, type (office, retail, industrial, residential, land), size (m² or sq ft), year built
- Ownership type: freehold, long leasehold, short leasehold, fund-owned
- Portfolio status: available, let, partially let, under development, for sale, sold
- Market valuation: record current valuation with date and valuer reference; history of past valuations
- Document storage: attach title deeds, planning permissions, energy performance certificates, surveys
- Property image gallery: photos of the property

### Advanced
- Multiple units: subdivide a building into individual lettable units, each with their own area and status
- EPC rating: energy performance certificate rating (A–G) with expiry date
- Service charge budget: record annual service charge budget and actuals for multi-tenant buildings
- Portfolio summary: aggregated view of total portfolio area, occupied area, and current valuation
- Bulk import: import property records from a CSV template

### AI-Powered
- Valuation trend analysis: chart historical valuations and estimate current market value movement
- Portfolio risk score: composite score factoring vacancy rate, lease expiry concentration, and maintenance backlog
- Document expiry alerts: flag EPC, planning permissions, and insurance policies approaching expiry

---

## Data Model

```erDiagram
    properties {
        ulid id PK
        ulid company_id FK
        string name
        string address_line_1
        string city
        string postcode
        string country
        string property_type
        string ownership_type
        decimal gross_area
        decimal net_lettable_area
        string area_unit
        integer year_built
        string portfolio_status
        decimal current_valuation
        date valuation_date
        string epc_rating
        date epc_expires_at
        timestamps created_at_updated_at
    }

    property_units {
        ulid id PK
        ulid property_id FK
        string unit_reference
        decimal area
        string status
        timestamps created_at_updated_at
    }

    properties ||--o{ property_units : "divided into"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `properties` | Property master records | `id`, `company_id`, `address_line_1`, `property_type`, `ownership_type`, `portfolio_status`, `current_valuation` |
| `property_units` | Lettable units | `id`, `property_id`, `unit_reference`, `area`, `status` |

---

## Permissions

```
realestate.properties.view
realestate.properties.create
realestate.properties.update
realestate.properties.delete
realestate.properties.view-financial
```

---

## Filament

- **Resource:** `App\Filament\Realestate\Resources\PropertyResource`
- **Pages:** `ListProperties`, `CreateProperty`, `EditProperty`, `ViewProperty`
- **Custom pages:** `PortfolioMapPage`, `PortfolioSummaryPage`
- **Widgets:** `TotalPortfolioValueWidget`, `VacancyRateWidget`
- **Nav group:** Properties

---

## Displaces

| Feature | FlowFlex | Yardi | MRI | Re-Leased |
|---|---|---|---|---|
| Property master record | Yes | Yes | Yes | Yes |
| Unit subdivision | Yes | Yes | Yes | Yes |
| Valuation history | Yes | Yes | Yes | Yes |
| AI portfolio risk score | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[lease-management]] — leases reference property and unit records
- [[property-maintenance]] — maintenance requests reference properties
- [[rental-billing-arrears]] — billing linked to lettable units
- [[ifrs-16-lease-accounting]] — IFRS 16 right-of-use assets linked to property
