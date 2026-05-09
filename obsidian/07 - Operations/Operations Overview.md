---
tags: [flowflex, domain/operations, overview, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Operations Overview

Physical operations management — inventory, assets, purchasing, field service, quality, safety, point of sale, and route optimization. All 10 modules built in Phase 4–5 as a complete panel.

**Filament Panel:** `operations`
**Domain Colour:** Amber `#D97706` / Light: `#FEF3C7`
**Domain Icon:** `heroicon-o-wrench-screwdriver`
**Phase:** 4–5 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[Inventory Management]] | Products, stock locations, stock levels, movements, adjustments, reorder rules, cycle counts |
| [[Asset Management]] | Physical asset register, assignments, check-in/out, lifecycle stages |
| [[Purchasing & Procurement]] | Suppliers, purchase orders, goods receipts, 3-way matching, approval thresholds |
| [[Equipment Maintenance]] | Preventive + reactive maintenance schedules, work orders, parts usage |
| [[Field Service Management]] | Job dispatch, technician GPS, mobile job sign-off, checklists, photos |
| [[Point of Sale]] | Tablet POS, sessions, transactions, cash/card payments, inventory sync |
| [[Quality Control & Inspections]] | Inspection templates, records, pass/fail, non-conformance reports |
| [[Supply Chain Visibility]] | Shipment tracking, carrier events, supplier performance scoring |
| [[HSE]] | Safety incidents, risk assessments, safety observations, investigations |
| [[Route Optimization & Dispatch]] | AI multi-stop route planning, live GPS tracking, proof of delivery, customer ETAs |
| [[Vendor Portal]] | Supplier self-service: PO visibility, invoice submission, compliance docs, delivery updates |

## Filament Panel Structure

**Navigation Groups:**
- `Inventory` — Products, Stock Locations, Stock Movements, Stock Adjustments, Cycle Counts
- `Assets` — Assets, Asset Categories, Asset Assignments
- `Procurement` — Suppliers, Purchase Orders, Goods Receipts
- `Maintenance` — Maintenance Schedules, Work Orders
- `Field Service` — Field Jobs, Technician Locations
- `Point of Sale` — POS Terminals, POS Sessions
- `Quality` — Inspection Templates, Inspection Records, NCRs
- `Supply Chain` — Shipments
- `Safety` — Incidents, Risk Assessments, Safety Observations

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `StockBelowReorderPoint` | Inventory Management | Purchasing (create draft PO) |
| `PurchaseOrderApproved` | Purchasing | Finance — AP/AR (create bill) |
| `PurchaseOrderReceived` | Purchasing | Inventory (update stock levels) |
| `WorkOrderCompleted` | Equipment Maintenance | Asset (update maintenance record) |
| `MaintenanceScheduled` | Equipment Maintenance | Notifications (remind technician) |
| `FieldJobCompleted` | Field Service | Invoice (create), Inventory (deduct parts), CRM (close ticket) |
| `FieldJobDispatched` | Field Service | Notifications (notify technician) |
| `POSTransactionCompleted` | Point of Sale | Inventory (deduct stock), Finance (record sale) |
| `InspectionFailed` | Quality Control | Notifications (notify ops manager) |
| `ShipmentDelayed` | Supply Chain | Notifications (notify procurement) |
| `IncidentReported` | HSE | Notifications (notify HSE manager) |
| `CriticalIncidentRaised` | HSE | Notifications (all managers) |

## Permissions Prefix

`operations.inventory.*` · `operations.assets.*` · `operations.procurement.*`  
`operations.maintenance.*` · `operations.field-service.*` · `operations.pos.*`  
`operations.quality.*` · `operations.supply-chain.*` · `operations.hse.*`

## Database Migration Range

`300000–399999`

## Related

- [[Inventory Management]]
- [[Asset Management]]
- [[Purchasing & Procurement]]
- [[Equipment Maintenance]]
- [[Field Service Management]]
- [[Point of Sale]]
- [[Quality Control & Inspections]]
- [[Supply Chain Visibility]]
- [[HSE]]
- [[Panel Map]]
- [[Build Order (Phases)]]
