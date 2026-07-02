---
domain: finance
module: accounts-payable
feature: payment-runs
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payment Runs

Batch approved bills into a single **payment run** by due date and execute them atomically.

- Select scheduled bills → generate a payment batch (SEPA export). *(assumed: pain.001 XML deferred;
  v1 = batch list / CSV export — see [[../unknowns]])*.
- Execution posts a **cash** entry to the General Ledger and transitions bills `scheduled → paid`.
- Atomic: the run executes all bills or none; a line-sum check is enforced (brick/money integer minor units).
- Permission `finance.ap.execute-run`.

## UI

- **Kind**: custom-page (batch)
- **Page**: "Payment run" under `/finance/ap/payment-runs`
- **Layout**: select scheduled bills by due date → batch preview with line-sum check → execute (SEPA/CSV export)
- **Key interactions**: pick bills → preview batch total (line-sum reconciliation) → execute run (atomic, all-or-none); download SEPA/CSV
- **States**: empty (no scheduled bills) · loading (preview building) · error (line-sum mismatch / partial failure → whole run rolls back) · selected (bills chosen for the run)
- **Gating**: `finance.ap.execute-run`

## Data

- Owns / writes: `fin_payment_runs`; updates `fin_bills` `scheduled → paid`. Money as integer minor units (cents) via brick/money; line-sum check enforced on integer totals.
- Reads: own tables (scheduled bills)
- Cross-domain writes: execution posts a **cash** GL entry via `LedgerService::post` — never writes `fin_journal_*` directly. Atomic all-or-none ([[../../../../security/data-ownership]])

## Relations

- Consumes: no events
- Feeds: posts to [[../../general-ledger/_module]] via `LedgerService::post`; settled bills reflected in [[ap-aging]]
- in-domain: consumes scheduled bills from [[bill-approval]]; calls `LedgerService::post` (finance.ledger)
- pain.001 XML export deferred *(assumed)* — v1 is batch list / CSV/SEPA export

## Related

- [[../_module|Accounts Payable]] · [[bill-approval]] · [[ap-aging]] · [[../../general-ledger/_module]]
