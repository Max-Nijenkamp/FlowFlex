---
tags: [flowflex, domain/operations, purchasing, procurement, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-06
---

# Purchasing & Procurement

Purchase orders, supplier management, and 3-way invoice matching.

**Who uses it:** Operations, finance, procurement team
**Filament Panel:** `operations`
**Depends on:** Core
**Phase:** 4

## Events Fired

- `PurchaseOrderApproved` → consumed by [[Accounts Payable & Receivable]] (creates bill record, updates committed spend)

## Events Consumed

- `StockBelowReorderPoint` (from [[Inventory Management]]) → creates draft purchase order

## Features

- **Purchase order builder** — line items, quantities, prices, delivery dates
- **Supplier approval lists** — only approved suppliers can receive POs
- **Goods receipt notes** — record receipt of goods against PO
- **3-way matching** — PO → goods receipt → supplier invoice must match before payment
- **Approval thresholds** — POs above £X require additional approval
- **Supplier portal** — external login for suppliers to confirm and acknowledge orders

## Related

- [[Operations Overview]]
- [[Inventory Management]]
- [[Accounts Payable & Receivable]]
- [[Budgeting & Forecasting]]
