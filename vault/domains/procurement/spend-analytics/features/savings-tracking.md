---
domain: procurement
module: spend-analytics
feature: savings-tracking
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Savings Tracking

Quantify realised savings: catalogue agreed price vs the actual price paid on the PO.

## Behaviour

- Savings per line = `(agreed_price − actual_price) × qty`, summed over the period.
- Only lines with a catalogue agreed price contribute (net-new items excluded *(assumed)*).
- Soft dep on catalogue.

## UI

- **Kind**: widget (on the spend dashboard).
- **Page**: none — `SavingsWidget` on `SpendAnalyticsDashboard`.
- **Layout**: stat (total savings) + table (item, agreed, actual, qty, saving).
- **Key interactions**: period follows dashboard filter; drill to line detail.
- **States**: hidden (catalogue inactive) · empty ("No savings tracked yet") · loading (skeleton) · error (toast) · selected (line table).
- **Gating**: `procurement.spend.view`.

## Data

- Owns / writes: nothing.
- Reads: `proc_catalogue_items` (agreed) + `ops_po_lines` (actual), read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: catalogue agreed prices + PO line actuals.
- Feeds: nothing.

## Test Checklist

### Unit
- [ ] Savings = (agreed price - actual paid) x qty via brick/money; negative savings reported as overpayment *(assumed)*

### Feature (Pest)
- [ ] Only catalogue-linked PO lines counted; tenant isolation enforced

### Livewire
- [ ] `SavingsWidget` renders realised savings; hidden when catalogue inactive

## Unknowns

- Baseline when no agreed price exists. `*(assumed: excluded)*`

## Related

- [[../_module|Spend Analytics]] · [[../../supplier-catalogue/features/catalogue-items]] · [[spend-breakdown]]
