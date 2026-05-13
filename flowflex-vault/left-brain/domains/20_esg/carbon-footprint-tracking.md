---
type: module
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 5
status: complete
migration_range: 930000–949999
last_updated: 2026-05-12
---

# Carbon Footprint Tracking

Collect, calculate, and track greenhouse gas emissions across Scope 1, 2, and 3. Foundation for all CSRD/GRI/TCFD climate reporting.

**Panel:** `esg`  
**Phase:** 5

---

## GHG Protocol Scope Definitions

| Scope | Definition | Examples |
|---|---|---|
| Scope 1 | Direct emissions owned/controlled | Company vehicles, gas boilers, on-site generators |
| Scope 2 | Indirect emissions from purchased energy | Office electricity, purchased heat/steam/cooling |
| Scope 3 | All other indirect emissions in value chain | Business travel, supply chain, employee commuting, product use, waste |

Scope 3 is typically 70-90% of total emissions but hardest to measure. CSRD requires Scope 1+2+3.

---

## Features

### Emission Data Sources
- **Manual entry**: meter readings, fuel receipts, travel expenses
- **Auto-import from Finance**: pull electricity/gas bills, fuel expense claims, travel bookings
- **Auto-import from Travel domain**: flights, hotels, car hire → auto-calculate emissions using ICAO/DEFRA factors
- **Auto-import from Fleet**: vehicle fuel logs → Scope 1 calculation
- **Supplier data requests**: send survey to suppliers → they submit their Scope 1+2 → becomes your Scope 3 Cat 1

### Emission Factors
- GHG Protocol emission factors (updated annually)
- DEFRA UK emission factors
- EPA US emission factors
- Country-specific electricity grid factors (changes yearly as grids get cleaner)
- Custom factors (for unusual activity data)
- Market-based vs location-based Scope 2 (for renewable energy certificates)

### Calculation Engine
- Activity data × emission factor = CO₂e (tonnes)
- Automatic unit conversion (kWh, litres, km, nights, kg)
- Currency uncertainty: flag emission factors with high uncertainty range
- Group by: category, scope, site, department, time period

### Dashboards
- Total emissions this year vs last year
- Breakdown by scope (pie chart)
- Breakdown by category (bar chart — travel vs energy vs supply chain)
- Intensity metric: tCO₂e per £1m revenue / per employee / per m² office space
- Progress against reduction targets

### Data Quality Tracking
- Mark each data input as: Measured / Estimated / Calculated / Default
- Data coverage: % of emissions with high-quality data
- Flag gaps (categories with no data — likely missing not zero)

---

## Data Model

```erDiagram
    emission_records {
        ulid id PK
        ulid company_id FK
        string scope
        string category
        string subcategory
        string activity_description
        decimal activity_quantity
        string activity_unit
        decimal emission_factor
        string emission_factor_source
        decimal co2e_tonnes
        date activity_period_start
        date activity_period_end
        string data_quality
        string source_type
        ulid source_id
    }

    emission_targets {
        ulid id PK
        ulid company_id FK
        integer baseline_year
        decimal baseline_co2e
        integer target_year
        decimal target_co2e
        decimal target_reduction_percent
        string target_type
        boolean sbti_validated
    }
```

---

## Permissions

```
esg.carbon.view
esg.carbon.enter-data
esg.carbon.manage-factors
esg.carbon.set-targets
esg.carbon.export
```

---

## Related

- [[MOC_ESG]]
- [[MOC_Operations]] — fleet fuel, supply chain
- [[left-brain/domains/19_travel/MOC_Travel.md]] — travel emissions auto-import
