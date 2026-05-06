---
tags: [flowflex, domain/operations, overview, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-06
---

# Operations Overview

Physical operations management — inventory, assets, purchasing, field service, quality, and safety.

**Filament Panel:** `operations`
**Domain Colour:** Amber `#D97706` / Light: `#FEF3C7`
**Domain Icon:** `wrench-screwdriver` (Heroicons)
**Phase:** 4 (core: Inventory, Asset Management, Purchasing) + 5 (full suite)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[Inventory Management]] | 4 | Stock levels, reorder alerts, warehouse |
| [[Asset Management]] | 4 | Physical asset tracking, check-in/out |
| [[Purchasing & Procurement]] | 4 | POs, supplier management, 3-way match |
| [[Equipment Maintenance]] | 5 | Preventive maintenance, work orders |
| [[Field Service Management]] | 5 | Job dispatch, GPS, mobile app |
| [[Supply Chain Visibility]] | 5 | Supplier to customer order tracking |
| [[Point of Sale]] | 5 | Tablet POS, inventory sync |
| [[Quality Control & Inspections]] | 5 | Digital checklists, NCR, corrective actions |
| [[HSE]] | 5 | Incident reporting, risk assessments |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `StockBelowReorderPoint` | [[Inventory Management]] | [[Purchasing & Procurement]] (create draft PO) |
| `FieldJobCompleted` | [[Field Service Management]] | [[Invoicing]] (create invoice), [[Inventory Management]] (deduct parts), CRM (close ticket) |
| `PurchaseOrderApproved` | [[Purchasing & Procurement]] | [[Accounts Payable & Receivable]] (create bill) |

## Related

- [[Inventory Management]]
- [[Asset Management]]
- [[Purchasing & Procurement]]
- [[Field Service Management]]
- [[Panel Map]]
