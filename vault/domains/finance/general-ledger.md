---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.ledger
status: planned
color: "#4ADE80"
---

# General Ledger

Chart of accounts, double-entry journal entries, and trial balance. All financial transactions from other modules post journal entries here. The source of truth for all financial reporting.

---

## Core Features

- Chart of accounts: hierarchical account structure (assets, liabilities, equity, revenue, expenses)
- Account types: Asset, Liability, Equity, Revenue, Expense
- Journal entries: debit/credit pairs, mandatory balance (debits = credits), reference, description
- Auto-posting: invoices, payments, expenses, payroll runs create journal entries automatically
- Trial balance report: by date range
- Account balance drill-down: click account → see all journal lines for that account
- Fiscal year close: lock previous periods to prevent retroactive edits

---

## Data Model

| Table | Key Columns |
|---|---|
| `fin_accounts` | company_id, code, name, type (asset/liability/equity/revenue/expense), parent_account_id, is_active |
| `fin_journal_entries` | company_id, reference, description, entry_date, status (draft/posted), created_by |
| `fin_journal_lines` | journal_entry_id, company_id, account_id, debit_cents, credit_cents, description |

```mermaid
erDiagram
    fin_accounts {
        ulid id PK
        ulid company_id FK
        string code
        string name
        string type
        ulid parent_account_id FK
    }
    fin_journal_entries {
        ulid id PK
        ulid company_id FK
        string reference
        date entry_date
        string status
    }
    fin_journal_lines {
        ulid id PK
        ulid journal_entry_id FK
        ulid account_id FK
        int debit_cents
        int credit_cents
    }
    fin_journal_entries ||--o{ fin_journal_lines : "has"
    fin_accounts ||--o{ fin_journal_lines : "posted to"
    fin_accounts }o--o| fin_accounts : "parent"
```

---

## Filament

**Nav group:** Ledger

- `ChartOfAccountsResource` — hierarchical account tree, create/edit accounts
- `JournalEntryResource` — list, create manual entries, view lines; auto-posted entries read-only
- `TrialBalancePage` (custom page) — date range selector, account balance table

---

## Related

- [[domains/finance/invoicing]]
- [[domains/finance/expenses]]
- [[domains/finance/financial-reporting]]
