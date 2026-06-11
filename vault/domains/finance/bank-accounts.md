---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.bank
status: planned
priority: v1-core
depends-on: [finance.ledger, core.billing, core.rbac, core.files]
soft-depends: [finance.invoicing, finance.expenses]
fires-events: []
consumes-events: []
patterns: [encryption, money, queues]
tables: [fin_bank_accounts, fin_bank_transactions]
permission-prefix: finance.bank
encrypted-fields: ["fin_bank_accounts.iban", "fin_bank_accounts.account_number"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Bank Accounts

Bank account records, manual transaction import (CSV), and reconciliation against posted journal entries.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | each bank account maps to a GL account; reconciliation matches journal lines |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, CSV uploads |
| Soft | [[domains/finance/invoicing\|finance.invoicing]], [[domains/finance/expenses\|finance.expenses]] | match targets; without them only manual journal matches |

---

## Core Features

- Bank account records: account name, bank name, IBAN/account number, currency, linked GL account
- Transaction import: upload bank statement CSV, map columns (date, description, amount) — chunked job per [[architecture/queue-jobs]]
- Reconciliation: match imported transactions to existing invoices/expenses/journal entries
- Suggested matches: same amount ± date window *(assumed: exact amount, ±5 days)*
- Unreconciled transaction list: highlight items needing a match
- Reconciliation status: open/reconciled per transaction
- Balance display: bank balance vs GL balance comparison
- IBAN/account number encrypted at rest ([[architecture/security]] required list)

---

## Data Model

### fin_bank_accounts

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| name / bank_name | string | not null | |
| 🔐 account_number | text | nullable | encrypted |
| 🔐 iban | text | nullable | encrypted; `iban_last4` string for display *(assumed)* |
| currency | string(3) | not null | |
| gl_account_id | ulid | not null FK fin_accounts | asset account |
| current_balance_cents | bigint | default 0 | updated on import |
| deleted_at | timestamp | nullable | |

### fin_bank_transactions

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), bank_account_id FK | ulid | | |
| transaction_date | date | not null | |
| description | string | not null | |
| amount_cents | bigint | not null | signed |
| import_hash | string | unique `(bank_account_id, import_hash)` | dedupe on re-import (date+amount+description hash) |
| reconciled_at | timestamp | nullable | |
| journal_line_id | ulid | nullable FK | match target |

**Indexes:** `(company_id, bank_account_id, reconciled_at)`

---

## DTOs

### CreateBankAccountData — name, bank_name (required), iban (nullable, IBAN checksum rule), currency, gl_account_id (asset-type account)
### ImportStatementData — bank_account_id, file (csv, max 10MB), column_map {date, description, amount} + date format
### ReconcileData — transaction_id, journal_line_id (amount must match exactly — "Amounts do not match.")

## Services & Actions

Interface→Service: `BankServiceInterface` → `BankService`.

- `import(ImportStatementData $data): DataImportResult` — queued chunked job; dedupes via import_hash; never aborts on bad row (error report)
- `suggestMatches(string $transactionId): Collection` — open invoices/expenses/journal lines, exact amount ± 5 days
- `reconcile(ReconcileData $data): void` — links + stamps; throws `AmountMismatchException`
- `unreconcile(string $transactionId): void`
- `balanceComparison(string $bankAccountId): array{bank: Money, gl: Money, diff: Money}`

---

## Filament

**Nav group:** Ledger

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `BankAccountResource` | #1 CRUD resource | masked IBAN (last4), balance comparison column |
| `BankTransactionResource` | #1 CRUD resource | unreconciled tab; import action (upload + mapping); reconcile action with suggestions |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('finance.bank.view-any') && BillingService::hasModule('finance.bank')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a rate limiter on the import action (e.g. N imports per company per minute) in addition to the queued chunked job.
- **Upload contract** (medium): Document MIME whitelist (text/csv) + the companies/{company_id}/bank-imports/ storage path so uploaded statements are tenant-isolated; reference Security upload rules in architecture/security.md.

---

## Permissions

`finance.bank.view-any` · `finance.bank.manage-accounts` · `finance.bank.import` · `finance.bank.reconcile` · `finance.bank.view-sensitive` (full IBAN)

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ImportBankStatementJob` | imports | on upload | import_hash unique constraint — re-import skips duplicates |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] IBAN/account number ciphertext in DB; masked display; `view-sensitive` gates full value
- [ ] Re-importing same statement creates zero duplicates (hash dedupe)
- [ ] Reconcile with mismatched amount rejected
- [ ] Suggested matches respect amount + date window
- [ ] Bad CSV rows land in error report, import continues
- [ ] Balance comparison math via brick/money

---

## Build Manifest

```
database/migrations/xxxx_create_fin_bank_accounts_table.php
database/migrations/xxxx_create_fin_bank_transactions_table.php
app/Models/Finance/{BankAccount,BankTransaction}.php
app/Data/Finance/{CreateBankAccountData,ImportStatementData,ReconcileData}.php
app/Contracts/Finance/BankServiceInterface.php
app/Services/Finance/BankService.php
app/Exceptions/Finance/AmountMismatchException.php
app/Jobs/Finance/ImportBankStatementJob.php
app/Filament/Finance/Resources/{BankAccountResource,BankTransactionResource}.php
database/factories/Finance/{BankAccountFactory,BankTransactionFactory}.php
tests/Feature/Finance/{BankImportTest,ReconciliationTest,BankEncryptionTest}.php
```

---

## Related

- [[domains/finance/general-ledger]]
- [[domains/finance/accounts-receivable]]
- [[architecture/patterns/encryption]]
