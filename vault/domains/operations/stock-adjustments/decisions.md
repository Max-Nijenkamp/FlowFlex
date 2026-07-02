---
domain: operations
module: stock-adjustments
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Stock Adjustments — Decisions & ADR Notes

## GL Write-Off Posting Deferred (Report, Not Event)

**Context:** A write-off/shrinkage adjustment has a financial impact that ideally hits the general ledger.

**Decision:** v1 does **not** auto-post to finance.ledger. Instead it records `value_impact_cents` per adjustment and exposes a write-off report by reason/period for a finance user to journal manually.

**Consequences:** No dependency on the GL module for v1 (finance.ledger is soft). Avoids designing the write-off → journal-entry mapping prematurely. Automating it later is a `StockWrittenOff` event consumed by finance.ledger — a future ADR. Keeps the data-ownership boundary clean (no finance writes from Operations).

---

## Threshold Approval, Self-Approval Blocked

**Decision:** Adjustments whose `value_impact_cents` exceeds a company threshold *(assumed €500)* enter `pending-approval` with stock untouched; approval (by a different user) applies the movement.

**Consequences:** A shrinkage/fraud control at the exact write points that matter (theft, write-off). Small corrections apply immediately; large ones need a second signer.

---

## Simple Status Flag, Not a State Machine

**Decision:** `status` is a plain `pending-approval` / `applied` flag rather than `spatie/laravel-model-states` — there is only one real transition.

**Consequences:** Less machinery; the single guard (approver ≠ adjuster, apply-once) lives in `AdjustmentService::approve`.
