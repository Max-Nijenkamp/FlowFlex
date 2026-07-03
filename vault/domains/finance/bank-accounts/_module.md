---
domain: finance
module: bank-accounts
type: module
module-key: finance.bank
priority: v1-core
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.files]
soft-depends: [finance.invoicing, finance.expenses]
fires-events: []
consumes-events: []
patterns: [encryption, money, queues]
tables: [fin_bank_accounts, fin_bank_transactions]
permission-prefix: finance.bank
encrypted-fields: ["fin_bank_accounts.iban", "fin_bank_accounts.account_number"]
color: "#4ADE80"
updated: 2026-07-03
---

# Bank Accounts

Bank account records, manual transaction import (CSV), and reconciliation against posted journal entries.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Module-key

`finance.bank`

**Priority:** v1-core  
**Panel:** finance  
**Permission prefix:** `finance.bank`  
**Tables:** `fin_bank_accounts`, `fin_bank_transactions`

## Purpose

Bank accounts mirror real-world accounts against a GL asset account. Statement lines are imported from CSV via a chunked queued job and matched against posted journal lines (invoices, expenses, manual entries) during reconciliation. IBAN and account number are encrypted at rest.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | each bank account maps to a GL account; reconciliation matches journal lines |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] | gating, permissions, CSV uploads |
| Soft | [[../invoicing/_module\|finance.invoicing]], [[../expenses/_module\|finance.expenses]] | match targets; without them only manual journal matches |

## Core Features

- Bank account records: account name, bank name, IBAN/account number, currency, linked GL account.
- Transaction import: upload bank statement CSV, map columns (date, description, amount) — chunked job per [[../../../architecture/queue-jobs]]; see [[features/csv-import]].
- Reconciliation: match imported transactions to existing invoices/expenses/journal entries — see [[features/reconciliation]].
- Suggested matches: same amount ± date window *(assumed: exact amount, ±5 days)*.
- Unreconciled transaction list: highlight items needing a match.
- Reconciliation status: open/reconciled per transaction.
- Balance display: bank balance vs GL balance comparison.
- IBAN/account number encrypted at rest ([[../../../security/encryption]]).

## Permissions

`finance.bank.view-any` · `finance.bank.manage-accounts` · `finance.bank.import` · `finance.bank.reconcile` · `finance.bank.view-sensitive` (full IBAN)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ImportBankStatementJob` | imports | on upload | `import_hash` unique constraint — re-import skips duplicates |

See [[../../../architecture/queue-jobs]].

## Test Checklist

- [ ] Tenant isolation: company A cannot see, import into, or reconcile company B bank accounts/transactions
- [ ] Module gating: artifacts hidden when `finance.bank` inactive
- [ ] IBAN/account number ciphertext in DB; masked display; `view-sensitive` gates full value
- [ ] Re-importing same statement creates zero duplicates (hash dedupe)
- [ ] Reconcile with mismatched amount rejected
- [ ] Suggested matches respect amount + date window
- [ ] Bad CSV rows land in error report, import continues
- [ ] Balance comparison math via brick/money

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
app/Filament/Finance/Pages/ImportStatementPage.php
resources/views/filament/finance/pages/import-statement-page.blade.php
database/factories/Finance/{BankAccountFactory,BankTransactionFactory}.php
tests/Feature/Finance/{BankImportTest,ReconciliationTest,BankEncryptionTest,ImportStatementPageTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_bank_accounts`, `fin_bank_transactions`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]). IBAN and account number are encrypted at rest.

| Direction | Event / Call | Counterpart |
|---|---|---|
| Reads | `fin_journal_lines` (read-only) for reconciliation matching | [[../general-ledger/_module\|finance.ledger]] |

## Entity Notes

- [[architecture]] — service, money, queued import, balance comparison
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, encryption, upload + rate limiter
- [[decisions]] — encryption choice, match window
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/csv-import]], [[features/reconciliation]]

## Related

- [[../general-ledger/_module]]
- [[../accounts-receivable/_module]]
- [[../../../architecture/patterns/encryption]]
- [[../../../glossary]]
