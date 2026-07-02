---
domain: operations
module: operations-reporting
feature: spend-analytics
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Spend Analytics & Supplier Performance

Purchasing spend by supplier/category/time, plus supplier on-time and accuracy.

## Behaviour

- Aggregates PO totals by supplier, category, and period.
- Supplier performance: on-time delivery (GRN vs PO expected) and order accuracy, reusing `SupplierService::performance`.
- Sections hidden when PO / suppliers modules are inactive (soft dep).

## UI

- **Kind**: widget — `SpendWidget` on `OperationsDashboardPage`; conditional on active modules.
- **Page**: widget on `OperationsDashboardPage` at `/operations/dashboard`.
- **Layout**: spend bar/line chart by supplier + category; supplier performance table (on-time %, order count); date filter.
- **Key interactions**: filter by supplier/category/period; drill to a supplier; Excel export; no writes.
- **States**: empty (no POs → "no purchasing activity") · loading (skeleton) · error (retry) · hidden (PO/suppliers module inactive) · selected (supplier row → supplier view).
- **Gating**: `operations.reporting.view`.

## Data

- Owns / writes: nothing.
- Reads: `ops_purchase_orders`, `ops_po_lines`, `ops_suppliers`, `ops_goods_receipts` (owned by their modules).
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: nothing (terminal reporting surface).
- Shared entity: PO/supplier/GRN tables (read-only).

## Related

- [[../_module|Operations Reporting]] · [[../../suppliers/features/supplier-performance|Supplier Performance]]
