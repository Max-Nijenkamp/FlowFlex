---
domain: procurement
module: goods-receipt
feature: match-evaluation
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# 3-Way Match Evaluation

Compare PO, GRN, and supplier bill for a purchase; auto-approve clean matches, flag discrepancies with a variance.

## Behaviour

- Match row auto-created per `(po_id, grn_id, bill_id)` when all three exist.
- `evaluate(billId)` computes `variance = bill − (GRN accepted qty × PO price)`; classifies quantity vs amount discrepancy.
- Within tolerance (±2% or €10 *(assumed)*) → `matched`, `approved_for_payment = true`.
- Outside tolerance → discrepancy status, awaits resolution.

## UI

- **Kind**: custom-page
- **Page**: "3-Way Match" (`/operations/procurement/matches`)
- **Layout**: queue list + a side-by-side compare pane (PO line | GRN received | bill line) with variance highlighted per line; status/variance columns in the queue.
- **Key interactions**: open a match → three-column compare; auto-approved rows badged green; discrepancies badged with variance; filter by status.
- **States**: empty ("No matches to review") · loading (queue skeleton) · error (toast + retry) · selected (compare pane open, mismatched cells highlighted).
- **Gating**: `procurement.goods-receipt.view-matches`.

## Data

- Owns / writes: `proc_three_way_matches`.
- Reads: PO+lines (`operations.purchase-orders`), GRN receipts (`operations.goods-receipt`), bill (`finance.ap`) — read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: PO/GRN/bill from Operations + Finance.
- Feeds: match verdict → [[payment-gate]] + [[discrepancy-resolution]].

## Test Checklist

### Unit
- [ ] Variance = bill - (GRN accepted qty x PO price) via brick/money integers; tolerance +/-2% or EUR 10 *(assumed)* classifies matched vs flagged
- [ ] Qty vs amount discrepancy classification

### Feature (Pest)
- [ ] Match row auto-created once per `(po, grn, bill)` triple when all three exist; within tolerance -> `approved_for_payment = true`
- [ ] Tenant isolation: matches per company

### Livewire
- [ ] `ThreeWayMatchResource` filter tabs (matched/flagged) render; read-heavy rows uneditable

## Unknowns

- Tolerance defaults + partial-receipt handling. **UNVERIFIED** ([[../unknowns]]).

## Related

- [[../_module|3-Way Match]] · [[discrepancy-resolution]] · [[payment-gate]]
