---
domain: finance
module: bank-accounts
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

See [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]].
