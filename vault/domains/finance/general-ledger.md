---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.ledger
status: planned
color: "#4ADE80"
---

# General Ledger

> Chart of accounts, double-entry journal entries, trial balance, and period close — the accounting foundation that all other Finance modules post into.

**Panel:** `finance`
**Module key:** `finance.ledger`

## What It Does

The General Ledger is the bedrock of FlowFlex Finance. Without it, FlowFlex Finance is a billing tool; with it, it is an accounting system. Every financial transaction from any module (invoice posted, expense approved, payroll run approved, asset depreciated) ultimately posts a double-entry journal to the GL. The chart of accounts defines the account structure. The GL view shows every transaction affecting each account over a period. Period close locks historical periods against backdated entries. Trial balance confirms that debits equal credits.

## Features

### Core
- Chart of accounts: account types (Asset, Liability, Equity, Revenue, Expense, COGS), hierarchy (Group → Account → Sub-account, 3 levels), numeric codes (4-digit default), lock accounts, tax mapping per account
- Journal entries: manual entries with date, description, and two or more lines (debit and credit); each line references an account, amount, and currency
- Auto-posted journals: other modules fire events that trigger journal lines automatically — invoice posted, payment received, expense approved, payroll approved, asset depreciated
- Trial balance: all accounts with debit/credit totals for a period — confirms debits = credits; export to Excel
- Standard COA templates: Netherlands (RGS), UK (FRS 102), US GAAP, Germany (SKR03/SKR04) — importable at setup

### Advanced
- Recurring journals: scheduled monthly or quarterly journals (accruals, prepayments, depreciation) — auto-post on configured date
- Journal approval workflow: entries above a threshold amount require second approval before posting
- Journal reversal: one-click reverse entry for corrections — original and reversal linked in the GL view
- GL drill-down: click any GL line to navigate to the source document (invoice, expense claim, payroll run)
- Period close: month-end close checklist → lock period → prevent backdated posting; year-end close zeros income/expense accounts to retained earnings

### AI-Powered
- Posting anomaly detection: AI flags journal entries with unusual account combinations or amounts significantly different from historical averages — surfaced for review before the entry is approved
- Accrual suggestions: at month-end, AI analyses open purchase orders and uninvoiced time entries and suggests which accrual journals to create

## Data Model

```erDiagram
    chart_of_accounts {
        ulid id PK
        ulid company_id FK
        ulid parent_id FK
        string code
        string name
        string type
        string currency
        boolean is_active
        boolean is_locked
        json tax_mapping
        timestamps created_at/updated_at
    }

    journal_entries {
        ulid id PK
        ulid company_id FK
        string reference
        string description
        date entry_date
        string status
        string source_type
        ulid source_id FK
        ulid created_by FK
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at/updated_at
    }

    journal_lines {
        ulid id PK
        ulid journal_entry_id FK
        ulid account_id FK
        decimal debit
        decimal credit
        string currency
        decimal debit_base
        decimal credit_base
        decimal fx_rate
        string description
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | Asset / Liability / Equity / Revenue / Expense / COGS |
| `source_type` / `source_id` | Polymorphic link to originating document |
| `debit_base` / `credit_base` | Amounts converted to company base currency |

## Permissions

- `finance.ledger.view-ledger`
- `finance.ledger.post-journal`
- `finance.ledger.approve-journal`
- `finance.ledger.manage-coa`
- `finance.ledger.close-period`

## Filament

- **Resource:** `ChartOfAccountsResource`, `JournalEntryResource`
- **Pages:** `ListChartOfAccounts`, `ListJournalEntries`, `CreateJournalEntry`, `ViewJournalEntry`
- **Custom pages:** `TrialBalancePage`, `GeneralLedgerViewPage`
- **Widgets:** `TrialBalanceStatusWidget` — balance check (debits = credits) on finance dashboard
- **Nav group:** Ledger (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero | Chart of accounts and general ledger |
| QuickBooks | Double-entry bookkeeping |
| Sage 50 | General ledger and journal entries |
| Exact Online | GL and financial accounting |

## Related

- [[invoicing]]
- [[expenses]]
- [[payroll]]
- [[fixed-assets]]
- [[financial-reporting]]
