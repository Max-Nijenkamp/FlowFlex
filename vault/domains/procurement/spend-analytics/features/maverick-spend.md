---
domain: procurement
module: spend-analytics
feature: maverick-spend
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Maverick Spend Detection

Flag spend that bypassed negotiated agreements: PO lines with no catalogue item, or from a non-approved supplier.

## Behaviour

- A line is maverick if it has no `catalogue_item_id` OR its supplier is not `approved` in `proc_supplier_status`.
- Aggregated as a value + list; soft dep — only when catalogue is active.
- Surfaces the "guided buying / tail spend" gap competitors under-serve ([[../../_opportunities]]).

## UI

- **Kind**: widget (on the spend dashboard) + a drill-down list.
- **Page**: none of its own — `MaverickSpendWidget` on `SpendAnalyticsDashboard`.
- **Layout**: stat (maverick % / value) + expandable list of offending lines (supplier, amount, reason).
- **Key interactions**: click stat → filtered line list; period follows the dashboard filter.
- **States**: hidden (catalogue inactive) · empty ("No maverick spend — nice") · loading (skeleton) · error (toast) · selected (line list open).
- **Gating**: `procurement.spend.view`.

## Data

- Owns / writes: nothing.
- Reads: `ops_po_lines`, `proc_catalogue_items`, `proc_supplier_status` (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: catalogue + supplier status ([[../../supplier-catalogue/_module]]); PO lines (Operations).
- Feeds: nothing.

## Test Checklist

### Unit
- [ ] Maverick rules: PO line with no catalogue item, or non-approved supplier

### Feature (Pest)
- [ ] Catalogue inactive -> maverick section hidden (soft dep)
- [ ] Tenant isolation on flagged lines

### Livewire
- [ ] `MaverickSpendWidget` renders flagged spend; hidden when catalogue inactive or without permission

## Unknowns

- Include non-preferred-supplier-within-catalogue as maverick? `*(assumed: off-catalogue OR non-approved)*`

## Related

- [[../_module|Spend Analytics]] · [[../../supplier-catalogue/features/preferred-supplier]] · [[spend-breakdown]]
