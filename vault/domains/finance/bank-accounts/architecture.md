---
domain: finance
module: bank-accounts
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Bank Accounts — Architecture

Interface→Service binding: `BankServiceInterface` → `BankService`, registered in the Finance service provider.

## Queued import

Statement import is a chunked queued job (`ImportBankStatementJob`, imports queue). It dedupes rows via the `import_hash` unique constraint (`date + amount + description` hash) and never aborts on a bad row — failures land in an error report and the import continues. See [[features/csv-import]] and [[../../../architecture/queue-jobs]].

## Reconciliation

`suggestMatches` proposes open invoices / expenses / journal lines with exact amount within a ±5-day window *(assumed)*. `reconcile` links a transaction to a journal line and stamps `reconciled_at`, throwing `AmountMismatchException` when the amounts differ. `unreconcile` clears the link. See [[features/reconciliation]].

## Money handling

All monetary columns are `bigint` integer **minor units** (cents), manipulated with `brick/money` — never raw float math. `amount_cents` on transactions is signed. `balanceComparison` returns `{bank: Money, gl: Money, diff: Money}` computed with brick/money. See [[../../../architecture/packages]] (brick/money).

## Encryption

`iban` and `account_number` are encrypted at rest (`encrypted` cast, `text` column). An `iban_last4` string is kept for masked display *(assumed)*. See [[security]] and [[../../../security/encryption]].

## Filament Artifacts

**Nav group:** Banking *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `BankAccountResource` | #1 CRUD resource | tweaks: custom-header-actions (manage-accounts) | encrypted `iban` + `account_number` masked to `iban_last4`; full value gated on `finance.bank.view-sensitive` |
| `BankTransactionResource` | #1 CRUD resource | tweaks: read-only-flow-owned (rows created by `ImportBankStatementJob`, mutated by reconcile) | open/reconciled status column; unreconciled filter (`reconciled_at IS NULL`) |
| `ImportStatementPage` | #7 wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] — upload CSV → map columns (date/description/amount) + date format → confirm → queued `ImportBankStatementJob`; realtime none | `/finance/bank/{account}/import`; see QUESTIONS — absent from Build Manifest |
| `ReconciliationPage` | #9 custom page (closest — two-panel matcher, no exact ui-strategy row) | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — unreconciled txns (left) vs suggested journal lines (right) + bank-vs-GL balance strip, click to link; realtime none | `/finance/bank/{account}/reconcile`; see QUESTIONS — a two-panel reconciliation matcher has no dedicated ui-strategy row |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.bank.view-any') && BillingService::hasModule('finance.bank')`
per [[../../../architecture/filament-patterns]] #1. `ImportStatementPage` and `ReconciliationPage` are custom pages and MUST state this
explicitly — Filament does not auto-gate custom pages; they additionally gate on `finance.bank.import` / `finance.bank.reconcile`.
No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Bank account CRUD (name, GL link, IBAN — form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Statement import — insert transactions + recompute balance | n-a | append-only insert deduped by the `(bank_account_id, import_hash)` unique constraint (idempotent re-import); `current_balance_cents` recomputed from own rows — no shared-edit path |
| Reconcile / unreconcile (link txn ↔ journal line, stamp `reconciled_at`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the transaction; amount-match check (`AmountMismatchException`) prevents double-linking a journal line under concurrent reconcile |
| Suggested matches / balance comparison (derived reads) | n-a | read-only computation over own txns + read-only journal lines — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]. Reconcile is not a GL/money posting (it only stamps a link on `fin_bank_transactions`), but is held at the pessimistic tier for its integrity guard against double-linking.

See [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]].
