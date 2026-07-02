---
domain: procurement
module: purchase-orders
feature: spend-commitment
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Commitment Tracking

Track committed spend (sent, not-yet-received PO totals) vs actual (received), so budget holders see money already promised, not just money already spent.

## Behaviour

- `CommitmentReport::for(period)` returns `{committed, actual}` in Money.
- Committed = Σ sent-but-unreceived PO totals; actual = Σ received. Committed excludes received (no double count).
- Feeds budget-vs-actual views and spend analytics.

## UI

- **Kind**: widget (commitment figures shown as columns/badges on `ProcurementPoResource` + a small stat widget; the full analytics page is spend-analytics).
- **Page**: none of its own — badges on the PO table + a "committed vs actual" stat widget in the Procurement nav.
- **Layout**: two-figure stat (committed / actual) with period filter; per-PO committed badge.
- **Key interactions**: change period → figures recompute; drill to the spend dashboard.
- **States**: empty (no sent POs → zeroes) · loading (stat skeleton) · error (toast) · n/a selected.
- **Gating**: `procurement.purchase-orders.view-commitments`.

## Data

- Owns / writes: nothing — a read/aggregation over Operations PO/receipt data + `proc_po_sourcing`.
- Reads: `ops_purchase_orders` / `ops_po_lines` / receipts (Operations, read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: PO + receipt data (Operations).
- Feeds: committed vs actual → [[../../spend-analytics/features/committed-vs-actual|spend analytics]]; budget-vs-actual (finance, read).

## Unknowns

- FX at commit vs receipt; partial receipts; cancelled POs. `*(assumed: PO-currency, committed = sent − received)*` — see [[../unknowns]].

## Related

- [[../_module|Procurement PO Layer]] · [[../../spend-analytics/features/committed-vs-actual]] · [[po-approval]]
