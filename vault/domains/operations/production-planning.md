---
type: module
domain: Operations
panel: operations
module-key: operations.production
status: planned
color: "#4ADE80"
---

# Production Planning

> Manage bills of materials, plan production runs, schedule capacity, and record material consumption and finished goods output.

**Panel:** `operations`
**Module key:** `operations.production`

## What It Does

Production Planning supports discrete and batch manufacturing without requiring a full-scale ERP. Bills of materials (BOMs) define the components and quantities required to produce each finished product. Production runs are planned against BOMs, scheduled to work centres with available capacity, and executed by recording material consumption from inventory and finished goods output back into stock. The module supports simple single-level BOMs for light assembly operations and multi-level BOMs for more complex manufacturing.

## Features

### Core
- Bill of materials (BOM): define finished product with component list (SKU, quantity, unit of measure)
- Multi-level BOM: sub-assemblies within a BOM with their own component lists
- Production run creation: select BOM, planned quantity, start date, end date, and work centre
- Material requirements: system calculates component quantities needed and checks stock availability
- Production order workflow: draft → planned → in-progress → completed → closed
- Output recording: confirm finished goods quantity produced; stock updated automatically
- Material consumption: record actual components used; variance from BOM reported

### Advanced
- Work centre management: define work centres with name, capacity (units per hour), and operating schedule
- Capacity scheduling: view work centre load across production orders; identify bottlenecks
- Capacity constraints: production planner can split an order across days to respect capacity limits
- Scrap recording: record scrapped components or finished units with reason code
- Production cost: compute actual cost per unit from component costs and labour time
- Production calendar: Gantt view of all production orders with work centre colour-coding

### AI-Powered
- Demand-driven planning: suggest production runs based on sales forecasts and current stock levels
- Bottleneck prediction: flag work centres likely to become capacity constraints in the next 30 days

## Data Model

```erDiagram
    ops_boms {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        string name
        string version
        boolean is_active
        timestamps timestamps
    }

    ops_bom_lines {
        ulid id PK
        ulid bom_id FK
        ulid component_id FK
        decimal quantity
        string uom
        boolean is_sub_assembly
    }

    ops_production_orders {
        ulid id PK
        ulid company_id FK
        ulid bom_id FK
        ulid work_centre_id FK
        integer qty_planned
        integer qty_produced
        string status
        date start_date
        date end_date
        decimal actual_cost
        timestamps timestamps
    }

    ops_work_centres {
        ulid id PK
        ulid company_id FK
        string name
        decimal capacity_per_hour
        json operating_schedule
        timestamps timestamps
    }

    ops_boms ||--o{ ops_bom_lines : "has"
    ops_boms ||--o{ ops_production_orders : "used in"
    ops_work_centres ||--o{ ops_production_orders : "hosts"
```

| Table | Purpose |
|---|---|
| `ops_boms` | Bill of materials header per finished product |
| `ops_bom_lines` | Component list with quantities |
| `ops_production_orders` | Planned and executed production runs |
| `ops_work_centres` | Production resources with capacity |

## Permissions

```
operations.production.view-any
operations.production.create
operations.production.execute
operations.production.manage-boms
operations.production.manage-work-centres
```

## Filament

**Resource class:** `BomResource`, `ProductionOrderResource`, `WorkCentreResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ProductionCalendarPage` (Gantt view), `CapacityPlanningPage` (work centre load overview)
**Widgets:** `ActiveProductionOrdersWidget`, `CapacityAlertWidget`
**Nav group:** Production

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Fishbowl Manufacturing | BOM and production order management |
| MRPeasy | Cloud MRP for SMB manufacturers |
| Katana MRP | Production planning and material requirements |
| SAP Business One Production | Full production planning module |

## Related

- [[inventory]] — component consumption and finished goods receipt
- [[purchase-orders]] — material shortages trigger purchase orders
- [[quality-control]] — production output inspection before stock entry
- [[warehousing]] — finished goods putaway after production
