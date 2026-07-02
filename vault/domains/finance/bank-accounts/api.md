---
domain: finance
module: bank-accounts
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Bank Accounts — DTOs, Services & Events

## DTOs

### CreateBankAccountData
name, bank_name (required), iban (nullable, IBAN checksum rule), currency, gl_account_id (must be an asset-type account).

### ImportStatementData
bank_account_id, file (csv, max 10MB), column_map `{date, description, amount}` + date format.

### ReconcileData
transaction_id, journal_line_id — amount must match exactly ("Amounts do not match.").

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

Interface→Service: `BankServiceInterface` → `BankService`.

- `import(ImportStatementData $data): DataImportResult` — queued chunked job; dedupes via `import_hash`; never aborts on a bad row (error report).
- `suggestMatches(string $transactionId): Collection` — open invoices/expenses/journal lines, exact amount ± 5 days.
- `reconcile(ReconcileData $data): void` — links + stamps `reconciled_at`; throws `AmountMismatchException`.
- `unreconcile(string $transactionId): void`.
- `balanceComparison(string $bankAccountId): array{bank: Money, gl: Money, diff: Money}`.

## Events

Fires: none. Consumes: none.

Reconciliation reads posted journal lines from [[../general-ledger/_module|finance.ledger]] directly (same-domain service read, no events).

See [[security]], [[../general-ledger/_module]], [[../financial-reporting/_module]].
