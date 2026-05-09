---
type: module
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 4
status: planned
migration_range: 300000–399999
last_updated: 2026-05-09
---

# Lot / Batch & Serial Number Tracking

Granular traceability for inventory. Lot tracking = groups of units (batch of 1000 tablets produced 2026-03-01). Serial tracking = individual units (laptop serial #SN-00123). Required by law in food, pharma, electronics, medical devices.

**Panel:** `operations`  
**Phase:** 4 — required alongside core Inventory Management

---

## Why Critical

- **Food/Pharma**: EU Food Law (EC 178/2002), FDA regulations require full lot traceability — if a contamination occurs, you must identify every affected unit within hours
- **Medical devices**: MDR (EU Medical Device Regulation) requires UDI (Unique Device Identification) tracking
- **Electronics/Machinery**: Warranty management requires knowing which serial number was sold to which customer
- **Recall management**: Without lot tracking, a recall means pulling ALL stock; with it, only the affected batch

---

## Features

### Lot Tracking
- Assign lot numbers on goods receipt (auto-generate or manual)
- Lot attributes: manufacture date, expiry date, country of origin, supplier lot number, QC status
- Picking strategy enforcement:
  - FEFO (First Expired First Out) — food, pharma
  - FIFO (First In First Out) — default
  - LIFO — specific use cases
  - Manual — operator chooses lot
- Lot status: quarantine / approved / rejected / recall
- Expiry alert: configurable days before expiry (e.g. 30 days warning)
- Lot split: one received lot split into sub-lots during production

### Serial Number Tracking
- Individual serial number per unit
- Auto-generate from sequence pattern or import from manufacturer
- Serial lifecycle: Received → In Stock → Sold → Returned → Repaired → Scrapped
- Link serial to: purchase order line, sales order line, customer contact, service history
- Warranty tracking: serial + purchase date → warranty expiry
- Duplicate serial detection (catch data entry errors)

### Forward & Backward Traceability
- **Forward trace**: Given lot L-123, which customers received it? (for recalls)
- **Backward trace**: Given customer order O-456, which lots/serials were in it? (for complaints)
- Full chain: supplier → receipt lot → production lot → sales order → customer
- One-click recall report: all orders containing affected lot, with customer contact details

### Manufacturing Integration
- BOM production: input component lots → output finished goods lot (lot genealogy)
- Lot consumed in production order → lot consumed record
- By-product lots (scrap, waste) tracked separately
- QC at lot level: inspection result stamped on lot record

### Reporting
- Lot expiry report (what's expiring when)
- Slow-moving lots (approaching expiry with no sales movement)
- Lot status summary per product
- Recall simulation ("if we recalled lot X, which orders are affected?")

---

## Data Model

```erDiagram
    inventory_lots {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        string lot_number
        date manufacture_date
        date expiry_date
        string country_of_origin
        string supplier_lot_number
        string status
        integer initial_quantity
        integer current_quantity
        ulid received_on_po_id FK
        timestamp created_at
    }

    inventory_serials {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid lot_id FK
        string serial_number
        string status
        ulid current_location_id FK
        ulid sold_on_order_id FK
        ulid sold_to_contact_id FK
        date warranty_expires_at
        timestamp created_at
    }

    lot_movements {
        ulid id PK
        ulid lot_id FK
        string movement_type
        integer quantity
        string reference_type
        ulid reference_id
        string from_location
        string to_location
        timestamp moved_at
        ulid moved_by FK
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `LotExpiryApproaching` | N days before expiry | Notifications (warehouse manager) |
| `LotRecalled` | Lot status → recall | Operations (block picking), Notifications (urgent), CRM (identify affected customers) |
| `SerialNumberDuplicated` | Duplicate detected on receipt | Notifications (warehouse manager — data error) |

---

## Permissions

```
operations.lot-tracking.view
operations.lot-tracking.create
operations.lot-tracking.quarantine
operations.lot-tracking.recall
operations.lot-tracking.trace
```

---

## Competitors Displaced

Fishbowl · inFlow · Cin7 · Unleashed · NetSuite (lot tracking module)

---

## Related

- [[MOC_Operations]]
- [[entity-product]]
- [[manufacturing-bom]] — production lot genealogy
- [[warehouse-management]] — lot-aware picking strategies
