---
domain: procurement
module: goods-receipt
feature: payment-gate
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Payment Gate

Block a PO-linked supplier bill from being approved/paid until its 3-way match is approved (or overridden).

## Behaviour

- Hook on `ApService::approveBill`: when procurement active + the bill is PO-linked, require `approved_for_payment` on the match; else raise `MatchFailedException`.
- Non-PO bills and inactive-module tenants are unaffected.
- On resolution, `ThreeWayMatchResolved` lets Finance's own listener release the hold.

## UI

- **Kind**: background (enforcement is server-side; it surfaces as a blocked state + reason on Finance's bill screen, not a procurement page).
- **Page**: none — the block manifests as a "held: awaiting 3-way match" state on the AP bill (Finance UI) with a deep link to the match board.
- **Key interactions**: none here; the user resolves via [[discrepancy-resolution]].
- **States**: n/a UI of its own; error surface = `MatchFailedException` message on the bill approve attempt.
- **Gating**: enforcement is unconditional when module active; resolving requires the resolve/override permissions.

## Data

- Owns / writes: nothing here (reads its own `proc_three_way_matches`).
- Reads: match state for the bill.
- Cross-domain writes: **none** — the gate is enforced inside Finance's own `approveBill`; procurement never writes `finance_ap_*` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: bill approval attempt from [[../../finance/accounts-payable/_module|finance.ap]].
- Feeds: `MatchFailedException` + `ThreeWayMatchResolved` back to Finance.

## Unknowns

- Whether a company can globally disable the hard gate (soft-warn mode). `*(assumed: hard gate when module active)*`

## Related

- [[../_module|3-Way Match]] · [[match-evaluation]] · [[../../finance/accounts-payable/_module]]
