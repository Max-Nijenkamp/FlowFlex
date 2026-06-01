---
type: domain-index
domain: Operations
panel: operations
color: "#4ADE80"
---

# Operations

Inventory, purchase orders, warehouses, suppliers, goods receipt, and stock adjustments. **Panel:** `/operations` (Orange) — Phase 3.

**This panel also hosts the Procurement domain** (see [[build/decisions/decision-2026-06-01-panel-consolidation]]). Procurement and Operations share the PO/GRN/supplier entities, so they run in one panel.

---

## Navigation Groups

- **Inventory** — Items, Stock Movements, Warehouses, Transfers, Adjustments
- **Purchasing** — Purchase Orders, Suppliers, Goods Receipt
- **Reporting** — Operations Dashboard, Spend Analytics
- **Procurement** (Procurement domain) — Requisitions, Sourcing, Supplier Catalogue, Approvals

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/operations/inventory\|Inventory]] | `operations.inventory` | planned | **P3 core** |
| [[domains/operations/purchase-orders\|Purchase Orders]] | `operations.purchase-orders` | planned | **P3 core** |
| [[domains/operations/warehouses\|Warehouses]] | `operations.warehouses` | planned | P3 |
| [[domains/operations/suppliers\|Suppliers]] | `operations.suppliers` | planned | P3 |
| [[domains/operations/goods-receipt\|Goods Receipt]] | `operations.goods-receipt` | planned | P3 |
| [[domains/operations/stock-adjustments\|Stock Adjustments]] | `operations.adjustments` | planned | P3 |
| [[domains/operations/operations-reporting\|Operations Reporting]] | `operations.reporting` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — PO status, GRN status
- `spatie/laravel-pdf` — PO PDFs
- Cross-domain: `PurchaseOrderReceived` / `GoodsReceived` → Finance AP + Inventory
- Integrates with [[domains/procurement/_index]] (requisitions → POs)
- Stock movements feed Finance GL (COGS, write-offs)
