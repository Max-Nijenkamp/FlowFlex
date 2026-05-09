---
type: module
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
status: planned
migration_range: 950000–953999
last_updated: 2026-05-09
---

# Property Register

Central portfolio of all owned, leased, or managed properties. Single source of truth for property metadata, valuation, tenure, and document storage.

---

## Core Functionality

### Property Record
Each property contains:
- **Identity**: name, address, postcode, country, coordinates (lat/lng for map view)
- **Tenure**: owned / leasehold / licensed / managed on behalf of third party
- **Type**: office / retail / industrial / warehouse / residential / mixed-use / land
- **Size**: gross internal area (GIA), net internal area (NIA), in m² or sq ft
- **Floor count, units count** (for multi-unit buildings)
- **Year built / year acquired**
- **Current use**: operational / investment / development / vacant
- **EPC rating**: Energy Performance Certificate (A–G), expiry date

### Ownership & Tenure
- Freehold: purchase price, acquisition date, ownership entity (for multi-entity groups)
- Leasehold: points to [[lease-management]] record
- Managed: managing agent contact, management agreement document

### Valuation
- Book value (historic cost)
- Latest market valuation: amount, date, valuer name, report document
- Valuation history: all past valuations logged
- Depreciation rate (if depreciable building — linked to Finance fixed assets)

### Document Vault
Per property, store:
- Title deeds / land registry extract
- Planning permission documents
- Energy Performance Certificate
- Fire risk assessment
- Asbestos survey
- Insurance certificate
- Building warranties
- Management agreements

All documents with: document type, date, expiry date, uploaded by.

---

## Map View

All properties shown on interactive map:
- Cluster view for large portfolios
- Colour coding by property type
- Click → property card summary
- Filter by: type, tenure, status, country

---

## Data Model

### `realestate_properties`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(300) | "Westfield House, Amsterdam" |
| address_line1 | varchar(300) | |
| address_line2 | varchar(300) | nullable |
| city | varchar(100) | |
| postcode | varchar(20) | |
| country | char(2) | ISO |
| latitude | decimal(10,7) | |
| longitude | decimal(10,7) | |
| tenure | enum | freehold/leasehold/licensed/managed |
| property_type | enum | office/retail/industrial/warehouse/residential/land/mixed |
| status | enum | operational/investment/development/vacant |
| gia_sqm | decimal(10,2) | |
| nia_sqm | decimal(10,2) | nullable |
| units_count | int | nullable |
| year_built | int | nullable |
| acquired_date | date | nullable |
| purchase_price | decimal(16,2) | nullable |
| currency | char(3) | |
| epc_rating | char(1) | nullable A–G |
| epc_expiry | date | nullable |
| owning_entity | varchar(200) | nullable |

---

## Migration

```
950000_create_realestate_properties_table
950001_create_realestate_property_valuations_table
950002_create_realestate_property_documents_table
```

---

## Related

- [[MOC_RealEstate]]
- [[lease-management]]
- [[tenant-occupancy-management]]
- [[property-maintenance]]
- [[ifrs-16-lease-accounting]]
- [[MOC_Finance]] — property valuations → fixed assets
