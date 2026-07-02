---
domain: procurement
module: spend-analytics
feature: committed-vs-actual
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Committed vs Actual (+ Budget)

Show money committed (sent, unreceived POs) alongside actual (received) spend, and — when finance.budgets is active — budget vs actual.

## Behaviour

- Committed = Σ sent-but-unreceived PO totals; actual = Σ received (reuses PO-layer [[../../purchase-orders/features/spend-commitment|commitment]] definition).
- Budget-vs-actual section reads `finance.budgets` (soft) — hidden when inactive.

## UI

- **Kind**: widget (on the spend dashboard).
- **Page**: none — a stat/chart block on `SpendAnalyticsDashboard`.
- **Layout**: committed vs actual bars; optional budget line/threshold overlay; period filter.
- **Key interactions**: period follows dashboard; hover for figures; drill to POs.
- **States**: empty (no POs → zeroes) · loading (skeleton) · error (toast) · budget section hidden when finance inactive.
- **Gating**: `procurement.spend.view`.

## Data

- Owns / writes: nothing.
- Reads: `ops_purchase_orders`/`ops_po_lines`/receipts (Operations); `finance.budgets` (soft, read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: PO/receipt data (Operations); budgets (finance, soft).
- Feeds: nothing.

## Unknowns

- Reconcile "actual" with Finance AP paid bills. **UNVERIFIED** ([[../unknowns]]).

## Related

- [[../_module|Spend Analytics]] · [[../../purchase-orders/features/spend-commitment]] · [[../../finance/budgets/_module]]
