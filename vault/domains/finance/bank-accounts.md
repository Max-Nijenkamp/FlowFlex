---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.bank
status: planned
color: "#4ADE80"
---

# Bank Accounts

> Bank account records, transaction import via CSV, reconciliation of bank transactions against GL entries, and unreconciled balance tracking.

**Panel:** `finance`
**Module key:** `finance.bank`

## What It Does

Bank Accounts manages the company's bank account records within FlowFlex and enables manual reconciliation against the General Ledger. Each bank account is configured with its GL asset account link. Finance imports bank statement transactions via CSV (or via open banking connection). The reconciliation view matches imported bank transactions against posted GL entries — marking each as reconciled when matched. Unreconciled items are the daily work of a bookkeeper. The closing balance per account is derived from the GL, keeping FlowFlex and the physical bank in sync.

## Features

### Core
- Bank account records: name, account number (last 4 digits displayed), currency, bank name, linked GL asset account
- Transaction import: upload bank statement CSV — map date, description, and amount columns — imported as `bank_transactions` rows
- Reconciliation view: side-by-side — imported bank transactions on the left, unreconciled GL entries on the right — match pairs manually
- Reconciled marking: matched pairs marked reconciled; unmatched items remain in the reconciliation queue
- Running balance: sum of all GL entries for the linked asset account shown as the book balance

### Advanced
- Auto-matching: system suggests matches between bank transactions and GL entries based on amount and date proximity — one-click accept
- Suspense account: unmatched bank transactions parked in a suspense account pending investigation — suspense balance surfaced on dashboard
- Bank statement period lock: mark a statement period as reconciled and locked — prevents modification of reconciled GL entries for that period
- Multiple accounts: company can register multiple bank accounts (operating, payroll, savings) — reconciliation tracked separately per account
- Open banking connection (future): Plaid/Nordigen integration to auto-import transactions without CSV — scaffolded as a pluggable adapter

### AI-Powered
- Description parsing: AI parses bank transaction descriptions to suggest the appropriate GL expense account for unmatched transactions — speeds up manual reconciliation
- Anomaly detection: transactions that are significantly larger than historical averages or posted at unusual times flagged for review before reconciliation

## Data Model

```erDiagram
    bank_accounts {
        ulid id PK
        ulid company_id FK
        string name
        string account_number_last4
        string currency
        string bank_name
        ulid gl_account_id FK
        boolean is_active
        timestamps created_at/updated_at
    }

    bank_transactions {
        ulid id PK
        ulid bank_account_id FK
        ulid company_id FK
        date transaction_date
        string description
        decimal amount
        string type
        boolean is_reconciled
        ulid matched_journal_line_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | credit / debit |
| `is_reconciled` | True when matched to a GL journal line |
| `matched_journal_line_id` | FK to `journal_lines` — set when reconciled |

## Permissions

- `finance.bank.view`
- `finance.bank.import-transactions`
- `finance.bank.reconcile`
- `finance.bank.manage-accounts`
- `finance.bank.lock-period`

## Filament

- **Resource:** `BankAccountResource`
- **Pages:** `ListBankAccounts`, `ViewBankAccount`
- **Custom pages:** `BankReconciliationPage` — side-by-side reconciliation interface
- **Widgets:** `UnreconciledTransactionsWidget` — count of unreconciled items on finance dashboard
- **Nav group:** Ledger (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Bank Reconciliation | Bank feed and reconciliation |
| QuickBooks Bank | Bank account reconciliation |
| Sage | Bank reconciliation module |
| Silverfin | Bank reconciliation automation |

## Implementation Notes

**Filament:** `BankReconciliationPage` is a custom `Page` — a side-by-side two-panel interface that cannot be built with standard Filament tables. Left panel: `bank_transactions` for the selected account filtered to `is_reconciled = false`. Right panel: unreconciled GL journal lines for the same asset account. Clicking a row on either side selects it; clicking a row on the other side completes a match pair via Livewire action `matchPair($bankTransactionId, $journalLineId)`. Both panels are Livewire components with server-rendered tables and client-side row-selection state managed by Alpine.js.

**CSV import:** The CSV column mapping step is a multi-step Livewire flow on `ViewBankAccount` — not a separate page. Step 1: file upload (Filament `FileUpload` field). Step 2: column mapper (dropdown per detected CSV header mapping to date/description/amount). Step 3: preview first 10 rows. Step 4: import runs as a queued `ImportBankStatementJob` using `Bus::batch()`.

**Open banking (future scaffolding):** The spec notes Plaid/Nordigen as a future option. Scaffold the bank feed as a pluggable adapter pattern now: `app/Contracts/Finance/BankFeedAdapterInterface.php` with methods `connect()`, `getTransactions(DateRange $range)`, `disconnect()`. The CSV import is the `CsvBankFeedAdapter`. The Plaid adapter can be added later without changing the reconciliation UI.

**AI features:** Description parsing for GL account suggestion calls `app/Services/AI/BankReconciliationService.php` with a prompt containing the transaction description and the company's chart of accounts list. Returns a ranked list of suggested account codes. No external AI required for anomaly detection — that is a statistical comparison (z-score against 90-day average transaction amount per description pattern).

**Missing from data model:** A `bank_reconciliation_periods` table is implied by the "bank statement period lock" feature but not defined. Add: `{ulid id, ulid bank_account_id, date period_start, date period_end, boolean is_locked, ulid locked_by, timestamp locked_at}`. Also: `bank_transactions.bank_account_id` FK needs an explicit `company_id` for `BelongsToCompany` — add `ulid company_id FK` if not already denormalized (the BelongsToCompany trait requires the column directly on the table).

## Related

- [[general-ledger]]
- [[accounts-receivable]]
- [[accounts-payable]]
- [[cash-flow]]
