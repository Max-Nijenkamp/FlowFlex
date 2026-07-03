---
domain: finance
module: bank-accounts
feature: reconciliation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Reconciliation

Imported transactions are matched against posted journal lines (invoices, expenses, manual entries).

- `suggestMatches(transactionId)` returns open invoices / expenses / journal lines with exact amount within a ±5-day window *(assumed)*.
- `reconcile(ReconcileData)` links a transaction to a `journal_line_id` and stamps `reconciled_at`; it throws `AmountMismatchException` when amounts differ ("Amounts do not match.").
- `unreconcile(transactionId)` clears the link, returning the transaction to the open list.
- The unreconciled list highlights transactions still needing a match (`reconciled_at IS NULL`).
- `balanceComparison(bankAccountId)` returns `{bank: Money, gl: Money, diff: Money}` so bank balance can be compared against the linked GL account.

## UI

- **Kind**: custom-page (two-pane matcher)
- **Page**: "Reconciliation" under `/finance/bank/{account}/reconcile`
- **Layout**: unreconciled bank txns on the left, suggested journal lines on the right; balance comparison strip (bank vs GL)
- **Key interactions**: `suggestMatches` exact-amount within a ±5-day window *(assumed)*, click to link a txn to a journal line, unreconcile, `balanceComparison` bank vs GL
- **States**: empty (nothing to reconcile / all matched) · loading (suggestions building) · error (`AmountMismatchException` on link) · selected (a txn + its suggested match)
- **Gating**: `finance.bank.reconcile` *(assumed)*

## Data

- Owns / writes: `fin_bank_transactions` — stamps `reconciled_at`, sets `journal_line_id` link. Money as integer minor units (cents) via brick/money.
- Reads: `fin_journal_lines` from [[../../general-ledger/_module]] (finance.ledger) — **read-only**
- Cross-domain writes: none — does NOT write the ledger; links are stamped only on own `fin_bank_transactions`. `AmountMismatchException` on amount mismatch ([[../../../../security/data-ownership]])

## Relations

- Consumes: no events; reads journal lines posted by finance.ledger (`LedgerService::post` upstream)
- Feeds: no cross-domain events
- in-domain: consumes transactions from [[csv-import]]; reads GL lines from finance.ledger read side (never writes `fin_journal_*`)

## Test Checklist

### Unit
- [ ] `suggestMatches` returns journal lines with exact amount within the ±5-day window *(assumed)* and excludes out-of-window / mismatched-amount lines
- [ ] `balanceComparison` computes `diff = bank − gl` via brick/money (signed integer minor units)

### Feature (Pest)
- [ ] `reconcile` links a transaction to a journal line and stamps `reconciled_at` under a pessimistic lock; a mismatched amount throws `AmountMismatchException` and links nothing
- [ ] `unreconcile` clears the link and returns the txn to the open list; tenant isolation — cannot match against another company's transactions or journal lines

### Livewire
- [ ] `ReconciliationPage` renders the two-panel matcher (unreconciled left, suggestions right) + balance strip and links on click; `canAccess` / action denied without `finance.bank.reconcile`

See [[../api]], [[../data-model]], [[../../general-ledger/_module]].
