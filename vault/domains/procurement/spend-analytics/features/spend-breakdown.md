---
domain: procurement
module: spend-analytics
feature: spend-breakdown
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Breakdown

Total spend sliced by supplier, category, and department, with a trend over time and top-suppliers ranking.

## Behaviour

- Aggregates PO/requisition spend by supplier, category, department for a period.
- Trend series over the window; top-N suppliers by spend.
- Cached per `(company, from, to)`.

## UI

- **Kind**: custom-page (dashboard + apex charts)
- **Page**: "Spend Analytics" (`/operations/procurement/spend`)
- **Layout**: filter bar (period, supplier, category) + charts (bar by supplier/category, line trend) + top-suppliers table; export button.
- **Key interactions**: change filters → charts recompute (from cache); drill into a supplier/category; export ([[export]]).
- **States**: empty ("No spend in this period") · loading (chart skeletons) · error (toast + retry) · selected (drill-down panel).
- **Gating**: `procurement.spend.view`.

## Data

- Owns / writes: nothing.
- Reads: `proc_requisitions`, `ops_purchase_orders`, `ops_po_lines` (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: requisition + Operations PO data.
- Feeds: nothing.

## Unknowns

- "Actual" definition (received vs invoiced vs paid). `*(assumed: PO/received)*` ([[../unknowns]]).

## Related

- [[../_module|Spend Analytics]] · [[maverick-spend]] · [[savings-tracking]]
