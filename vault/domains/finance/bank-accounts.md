---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.bank
status: planned
color: "#4ADE80"
---

# Bank Accounts

Bank account records, manual transaction import (CSV), and reconciliation against posted journal entries.

---

## Core Features

- Bank account records: account name, bank name, IBAN/account number, currency, linked GL account
- Transaction import: upload bank statement CSV, map columns (date, description, amount)
- Reconciliation: match imported transactions to existing invoices/expenses/journal entries
- Unreconciled transaction list: highlight items needing a match
- Reconciliation status: open/reconciled per transaction
- Balance display: bank balance vs GL balance comparison

---

## Data Model

| Table | Key Columns |
|---|---|
| `fin_bank_accounts` | company_id, name, bank_name, account_number, iban, currency, gl_account_id, current_balance_cents |
| `fin_bank_transactions` | company_id, bank_account_id, transaction_date, description, amount_cents, reconciled_at, journal_line_id |

---

## Filament

**Nav group:** Ledger

- `BankAccountResource` — list, create, edit bank accounts
- `BankTransactionResource` — import CSV, list transactions, mark as reconciled

---

## Related

- [[domains/finance/general-ledger]]
- [[domains/finance/accounts-receivable]]
