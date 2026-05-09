---
type: module
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 5
status: planned
migration_range: 300000–399999
last_updated: 2026-05-09
---

# Manufacturing & Bill of Materials (BOM)

Bill of materials, production orders, material requirements planning (MRP), work order costing, and production scheduling for manufacturing companies. Replaces MRPeasy, Katana MRP, and Fishbowl Manufacturing.

**Panel:** `operations`  
**Phase:** 5

---

## Features

### Bill of Materials
- Multi-level BOM (finished product → sub-assemblies → raw materials)
- BOM versioning (v1, v2, v3 with change log)
- Component substitutions (alternate parts list)
- BOM costing (standard cost roll-up from component purchase prices)
- BOM import from CSV / ERP export
- By-product and scrap definitions (waste product of manufacturing process)

### Production Orders
- Create production order from sales order or manually
- Production order quantities and planned dates
- Auto-generate pick list (components needed from warehouse)
- Component reservation (reserve stock on production order creation)
- Work instructions per step (linked documents)
- Production stages (e.g. Cut → Assemble → QC → Pack)
- Stage-by-stage progress tracking

### Material Requirements Planning (MRP)
- Demand signals: confirmed sales orders + forecast demand
- Calculate gross requirements from BOM
- Net requirements = gross - on-hand - on-order
- Auto-generate draft purchase orders for shortfalls
- Auto-generate draft production orders for sub-assemblies
- MRP run: nightly scheduled or on-demand
- Pegging: trace requirement back to which sales order triggered it

### Work Orders & Labour
- Work orders per production stage
- Assign to operator(s) or work centre
- Time tracking per work order (actual vs standard)
- Machine runtime logging
- Labour cost calculation (actual hours × labour rate)
- Work order completion triggers stock movement (components consumed, finished goods added)

### Production Costing
- Standard cost vs actual cost per production order
- Variance reporting (why did this batch cost more than standard?)
- WIP (Work in Progress) inventory valuation
- Overhead absorption (apply overhead rate per machine hour)
- Cost roll-up: raw material + labour + overhead = finished goods COGS

### Quality at Production
- Quality checkpoint per stage (pass/fail)
- Non-conformance report (NCR) creation from production
- Scrap recording (component destroyed during production)
- Rework order (failed product sent back through production)

---

## Data Model

```erDiagram
    bills_of_materials {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        integer version
        boolean is_active
        decimal standard_cost
    }

    bom_components {
        ulid id PK
        ulid bom_id FK
        ulid component_product_id FK
        decimal quantity
        string unit_of_measure
        boolean is_optional
        string scrap_percentage
    }

    production_orders {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid bom_id FK
        decimal quantity
        string status
        date planned_start
        date planned_end
        date actual_start
        date actual_end
        decimal actual_cost
    }

    work_orders {
        ulid id PK
        ulid production_order_id FK
        string stage_name
        string status
        ulid assigned_to FK
        decimal planned_hours
        decimal actual_hours
        timestamp completed_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `ProductionOrderCreated` | PO raised | Inventory (reserve components), Notifications (production manager) |
| `ProductionOrderCompleted` | All stages done | Inventory (add finished goods to stock), Finance (post COGS) |
| `MRPRunCompleted` | MRP calculation done | Operations (new draft POs/production orders created), Notifications |
| `ProductionQualityFailed` | QC checkpoint failed | Operations (NCR created), Notifications |

---

## Permissions

```
operations.manufacturing.view-any
operations.manufacturing.create-orders
operations.manufacturing.run-mrp
operations.manufacturing.manage-bom
operations.manufacturing.record-production
```

---

## Competitors Displaced

MRPeasy · Katana MRP · Fishbowl Manufacturing · Odoo Manufacturing · NetSuite Manufacturing · inFlow (manufacturing)

---

## Related

- [[MOC_Operations]]
- [[entity-product]]
- [[inventory-management]] — component stock, finished goods stock
- [[purchasing-procurement]] — MRP triggers purchase orders
- [[quality-control-inspections]] — QC during production
