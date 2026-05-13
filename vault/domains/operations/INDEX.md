---
type: domain-index
domain: Operations
panel: operations
panel-path: /operations
panel-color: Orange
color: "#4ADE80"
---

# Operations

One panel to manage inventory, purchasing, warehousing, quality, production, suppliers, logistics, and fleet — replacing the patchwork of TradeGecko, Fishbowl, and spreadsheets that most growing businesses use.

**Panel:** `operations` — `/operations`
**Filament color:** Orange

---

## Modules

| Module | Key | Description |
|---|---|---|
| [[inventory]] | operations.inventory | SKU management, stock levels, reorder points, and stock movements |
| [[purchase-orders]] | operations.purchase-orders | PO creation, approval workflow, goods receipt, and supplier matching |
| [[warehousing]] | operations.warehouse | Warehouse zones, bin locations, putaway rules, and pick lists |
| [[quality-control]] | operations.quality | Inspection checklists, non-conformance reports, and supplier quality tracking |
| [[production-planning]] | operations.production | Production runs, BOM management, and capacity scheduling |
| [[supplier-management]] | operations.suppliers | Supplier records, performance ratings, contract terms, and assessments |
| [[logistics]] | operations.logistics | Shipment tracking, carrier management, and delivery scheduling |
| [[fleet-management]] | operations.fleet | Vehicle records, maintenance schedules, fuel tracking, and driver assignment |

---

## Nav Groups

- **Inventory** — inventory, purchase-orders, supplier-management
- **Warehouse** — warehousing, quality-control
- **Production** — production-planning
- **Logistics** — logistics, fleet-management
- **Settings** — units of measure, warehouses, carriers, reorder rules

---

## Displaces

| Tool | Replaced By |
|---|---|
| TradeGecko / Cin7 | inventory, purchase-orders, warehousing |
| Fishbowl | inventory, production-planning, warehousing |
| SAP Business One (ops modules) | All eight modules combined |
| Webfleet / Fleetio | fleet-management |
| Deposco WMS | warehousing |

---

## Related

- [[../ecommerce/INDEX]] — ecommerce orders trigger fulfilment workflows
- [[../finance/INDEX]] — purchase orders create supplier invoices
- [[../analytics/INDEX]] — operations KPIs feed BI dashboards
- [[../crm/INDEX]] — supplier contacts link to CRM company records
