---
domain: procurement
module: goods-receipt
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# 3-Way Match — Architecture

## Shape

A single service (`ThreeWayMatchService`) that reconciles three documents owned by three domains, storing only its own verdict rows, and a read-hook that lets Finance's AP enforce the payment gate.

```mermaid
flowchart TD
    BILL`finance.ap bill` -->|links po_id, grn_id| EV[ThreeWayMatchService.evaluate]
    PO`operations PO + lines` -->|price/qty| EV
    GRN`operations GRN receipts` -->|received qty| EV
    EV -->|variance + status| MT[(proc_three_way_matches)]
    EV -->|within tolerance| AUTO[approved_for_payment = true]
    RES[ThreeWayMatchService.resolve] -->|override/reject + notes| MT
    RES --> RV{{ThreeWayMatchResolved}}
    AP`finance.ap ApService.approveBill` -->|reads match state| GATE{approved_for_payment?}
    GATE -->|no| EX[MatchFailedException]
```

## Match logic

- Triple `(po_id, grn_id, bill_id)` — one match row per triple, auto-created when all three exist.
- Tolerance: ±2% or €10 *(assumed defaults, configurable)*. Within tolerance → `matched` + `approved_for_payment = true`.
- Variance = `bill − (GRN accepted qty × PO price)`; qty vs amount discrepancy classified.

## Key decisions

- **Read-only reconciliation.** Reads PO, GRN, bill; writes only `proc_three_way_matches`. The AP gate is a hook that *reads* match state and raises `MatchFailedException` — it does not write AP tables.
- **Auto-approve within tolerance** to avoid manual work on clean matches (addresses the "22% exception, manual triage" pain — [[../_opportunities]]).
- **Override is the audited exception path**, permission-gated.
- **Money** integer cents (brick/money) for all variance math.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../finance/accounts-payable/_module]] · [[../../../architecture/event-bus]]
