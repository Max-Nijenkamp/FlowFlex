---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: planned
migration_range: 200000–249999
last_updated: 2026-05-09
---

# General Ledger & Chart of Accounts

Double-entry bookkeeping foundation. Every financial transaction posts a debit and credit to the GL. Chart of Accounts defines the account structure. Without this, FlowFlex Finance is a billing tool, not an accounting system. Replaces the GL in Xero, QuickBooks, Exact, AFAS.

**Panel:** `finance`  
**Phase:** 3 — foundation for all other Finance modules

---

## Why This Needs Explicit Spec

Invoicing, AP/AR, Bank Reconciliation, and Payroll all *post journal entries* but where they post to is undefined without a GL. The COA determines:
- Which account code an invoice revenue posts to (4000 - Sales Revenue)
- Which account a supplier bill debits (5000 - Cost of Goods)
- Where payroll expense lands (6000 - Salary Expense)
- How the balance sheet balances

---

## Features

### Chart of Accounts
- Account types: Asset, Liability, Equity, Revenue, Expense, Cost of Goods
- Account hierarchy: Group → Account → Sub-account (3 levels)
- Account codes: numeric (flexible schema — 4-digit default, custom allowed)
- Standard COA templates: Netherlands (RGS), UK (FRS 102), US (GAAP), Germany (SKR03/SKR04)
- Import COA from CSV (migration from existing accounting system)
- Lock accounts (prevent posting to deprecated accounts)
- Tax mapping per account (which VAT rate applies)
- Currency per account (for foreign currency bank accounts)

### Journal Entries
- Manual journal entries (with description, date, lines)
- Recurring journals (monthly accruals, prepayments, depreciation)
- Auto-posted journals from other modules:
  - Invoice posted → debit Accounts Receivable, credit Revenue
  - Payment received → debit Bank, credit Accounts Receivable
  - Supplier bill → debit Expense, credit Accounts Payable
  - Payroll run → debit Salary Expense, credit Payroll Liability + Bank
  - Asset depreciation → debit Depreciation Expense, credit Accumulated Depreciation
- Journal approval workflow (above threshold amount requires second approval)
- Journal reversal (one-click reverse entry for corrections)

### General Ledger View
- Date-range filter per account
- Opening balance + transactions + closing balance
- Drill down from GL line to source document (click invoice ID → open invoice)
- Multi-currency GL (show transactions in original currency + base currency)

### Trial Balance
- All accounts with debit/credit totals for period
- Confirms debits = credits (balance check)
- Export to Excel for accountant review
- Compare two periods side-by-side

### Period Close
- Month-end close checklist: reconcile bank, clear suspense accounts, post accruals, approve journals
- Lock closed periods (prevent backdated posting after close)
- Year-end close: zero out income/expense accounts to retained earnings
- Reopen period (with permission + audit log)

### Opening Balances
- On first setup: import opening balances per account (for companies migrating mid-year)
- Suspense account for balancing differences during migration

---

## Standard Account Code Structure (Default)

```
1000–1999  Assets
  1000     Cash & Bank
  1100     Accounts Receivable
  1200     Inventory
  1500     Fixed Assets
  1600     Accumulated Depreciation

2000–2999  Liabilities
  2000     Accounts Payable
  2100     Payroll Liabilities
  2200     VAT Payable
  2300     Long-term Debt

3000–3999  Equity
  3000     Share Capital
  3100     Retained Earnings
  3200     Current Year Earnings

4000–4999  Revenue
  4000     Sales Revenue
  4100     Service Revenue
  4200     Other Income

5000–5999  Cost of Goods
  5000     Cost of Goods Sold
  5100     Direct Labour

6000–6999  Operating Expenses
  6000     Salaries & Wages
  6100     Rent & Facilities
  6200     Marketing & Advertising
  6300     Software & Subscriptions
  6400     Travel & Entertainment
```

---

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
    }

    journal_entries {
        ulid id PK
        ulid company_id FK
        string reference
        string description
        date entry_date
        string status
        string source_type
        ulid source_id
        ulid created_by FK
        ulid approved_by FK
        timestamp approved_at
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
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `JournalEntryPosted` | Manual or auto-post | Analytics (GL data) |
| `PeriodClosed` | Month-end lock | Finance (prevent backdating), Notifications |
| `TrialBalanceImbalance` | Debits ≠ credits detected | Notifications (finance manager — urgent) |

---

## Permissions

```
finance.gl.view-ledger
finance.gl.post-journal
finance.gl.approve-journal
finance.gl.manage-coa
finance.gl.close-period
finance.gl.reopen-period
```

---

## Related

- [[MOC_Finance]]
- [[entity-invoice]]
- [[MOC_Operations]] — purchase orders → AP journal
- [[MOC_HR]] — payroll → salary expense journal
