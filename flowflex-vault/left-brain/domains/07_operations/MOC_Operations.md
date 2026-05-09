---
type: moc
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 4
color: "#D97706"
last_updated: 2026-05-08
---

# Operations & Field Service — Map of Content

Physical operations: inventory, assets, procurement, field service, quality, safety, POS, and route optimisation.

**Panel:** `operations`  
**Phase:** 4–5  
**Migration Range:** `300000–399999`  
**Colour:** Amber `#D97706` / Light: `#FEF3C7`  
**Icon:** `heroicon-o-wrench-screwdriver`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Inventory Management | 4 | planned | Products, stock locations, movements, reorder rules |
| Asset Management | 4 | planned | Physical asset register, assignments, lifecycle |
| Purchasing & Procurement | 4 | planned | Suppliers, POs, goods receipts, 3-way matching |
| [[equipment-maintenance-cmms\|Equipment Maintenance (CMMS)]] | 4 | planned | Preventive + reactive maintenance, work orders, downtime tracking |
| Field Service Management | 4 | planned | Job dispatch, GPS tracking, mobile sign-off |
| Point of Sale | 4 | planned | Tablet POS, sessions, cash/card payments |
| [[quality-management-qms\|Quality Management (QMS)]] | 5 | planned | NCRs, CAPA, audits, ISO 9001, supplier quality |
| [[returns-management-rma\|Returns Management (RMA)]] | 5 | planned | RMA workflow, inspection, restocking, refund trigger |
| Supply Chain Visibility | 5 | planned | Shipment tracking, carrier events, supplier scoring |
| HSE | 5 | planned | Safety incidents, risk assessments, investigations |
| Route Optimisation & Dispatch | 5 | planned | AI multi-stop routing, live GPS, POD |
| Vendor Portal | 5 | planned | Supplier self-service: PO visibility, invoice submission |
| [[warehouse-management\|Warehouse Management System]] | 5 | planned | Bin locations, pick/pack/ship, barcode scanning, labour tracking |
| [[fleet-management\|Fleet Management]] | 5 | planned | Vehicle register, GPS tracking, driver compliance, fuel management |
| [[manufacturing-bom\|Manufacturing & BOM]] | 5 | planned | Multi-level BOM, production orders, MRP engine, work order costing |
| [[lot-batch-serial-tracking\|Lot / Batch & Serial Number Tracking]] | 4 | planned | Lot traceability, FEFO/FIFO, recall management, serial warranty |
| [[demand-planning-forecasting\|Demand Planning & Inventory Forecasting]] | 5 | planned | Statistical forecasting, ABC-XYZ analysis, reorder recommendations |
| [[supplier-qualification-onboarding\|Supplier Qualification & Onboarding]] | 5 | planned | Self-registration portal, document collection, scoring, approval |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `StockBelowReorderPoint` | Inventory | Purchasing (create draft PO) |
| `PurchaseOrderApproved` | Purchasing | Finance (create bill), Inventory (expect receipt) |
| `PurchaseOrderReceived` | Purchasing | Inventory (update stock) |
| `WorkOrderCompleted` | Equipment Maintenance | Asset (update maintenance record) |
| `FieldJobCompleted` | Field Service | Finance (create invoice), Inventory (deduct parts), CRM (close ticket) |
| `POSTransactionCompleted` | Point of Sale | Inventory (deduct stock), Finance (record sale) |
| `InspectionFailed` | Quality Control | Notifications (ops manager) |
| `IncidentReported` | HSE | Notifications (HSE manager) |
| `CriticalIncidentRaised` | HSE | Notifications (all managers) |

---

## Permissions Prefix

`operations.inventory.*` · `operations.assets.*` · `operations.procurement.*`  
`operations.maintenance.*` · `operations.field-service.*` · `operations.pos.*`  
`operations.quality.*` · `operations.hse.*`

---

## Competitors Displaced

NetSuite Inventory · Fishbowl · ServiceMax · Jobber · Square POS · Anvyl (supply chain)

---

## Related

- [[MOC_Domains]]
- [[entity-product]]
- [[MOC_Finance]] — PO → bill, field job → invoice
- [[MOC_CRM]] — field job completion → ticket closure
