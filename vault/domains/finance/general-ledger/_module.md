---
domain: finance
module: general-ledger
type: module
module-key: finance.ledger
priority: v1-core
build-status: planned
status: wip
depends-on: [core.billing, core.rbac, core.settings]
soft-depends: [finance.invoicing, finance.expenses, hr.payroll]
fires-events: []
consumes-events: [PayrollRunApproved]
patterns: [service, money, custom-pages, events]
tables: [fin_accounts, fin_journal_entries, fin_journal_lines, fin_fiscal_periods]
permission-prefix: finance.ledger
encrypted-fields: []
color: "#4ADE80"
updated: 2026-07-03
---

# General Ledger

Chart of accounts, double-entry journal entries, and trial balance. All financial transactions from other modules are intended to post journal entries here. The source of truth for all financial reporting — the Finance anchor, intended to be built first in `/finance`.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Module-key

`finance.ledger`

**Priority:** v1-core  
**Panel:** finance  
**Permission prefix:** `finance.ledger`  
**Tables:** `fin_accounts`, `fin_journal_entries`, `fin_journal_lines`, `fin_fiscal_periods`

## Purpose

The ledger is the only sanctioned write path for financial truth. Every invoice payment, approved expense, and payroll run is meant to land here as a balanced journal entry through `LedgerService::post` — never raw inserts. Posted entries are immutable; corrections happen via reversals.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Hard | [[../../core/company-settings/_module\|core.settings]] | base currency, fiscal year start |
| Soft | [[../invoicing/_module\|finance.invoicing]], [[../expenses/_module\|finance.expenses]], [[../../hr/payroll/_module\|hr.payroll]] | auto-posting sources; without them only manual entries exist |

## Core Features

- Chart of accounts: hierarchical structure (assets, liabilities, equity, revenue, expenses); default CoA seeded per company *(assumed: standard SME chart on module activation)*.
- Account types: Asset, Liability, Equity, Revenue, Expense.
- Journal entries: debit/credit pairs, mandatory balance (debits = credits), reference, description.
- Auto-posting: invoices, payments, expenses, payroll runs create journal entries via `LedgerService::post`.
- Trial balance report by date range.
- Account balance drill-down: account → all journal lines.
- Fiscal year close: lock previous periods against retroactive edits (`fin_fiscal_periods`).

## Permissions

`finance.ledger.view-any` · `finance.ledger.view` · `finance.ledger.post-manual` · `finance.ledger.reverse` · `finance.ledger.manage-accounts` · `finance.ledger.close-period`

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:finance:trial-balance:{from}:{to}` | 1 h (closed periods only) | posting into the period (writer busts) |

See [[../../../architecture/caching]].

## Test Checklist

- [ ] Tenant isolation: company A cannot see, post to, or reverse company B accounts/journals
- [ ] Module gating: artifacts hidden when `finance.ledger` inactive
- [ ] Unbalanced entry rejected (`UnbalancedEntryException`)
- [ ] Posting into closed period rejected (`ClosedPeriodException`); listener retries
- [ ] Posted entries immutable — no update/delete path; reversal creates mirror
- [ ] `PayrollRunApproved` posts balanced payroll entry per contract
- [ ] Trial balance debits = credits over fixture data (brick/money)
- [ ] Account with posted lines cannot be deleted
- [ ] Default CoA seeded on activation

## Build Manifest

```
database/migrations/xxxx_create_fin_accounts_table.php
database/migrations/xxxx_create_fin_journal_entries_table.php
database/migrations/xxxx_create_fin_journal_lines_table.php
database/migrations/xxxx_create_fin_fiscal_periods_table.php
app/Models/Finance/{Account,JournalEntry,JournalLine,FiscalPeriod}.php
app/Data/Finance/{CreateJournalEntryData,JournalEntryData,TrialBalanceData}.php
app/Contracts/Finance/LedgerServiceInterface.php
app/Services/Finance/LedgerService.php
app/Providers/Finance/FinanceServiceProvider.php
app/Exceptions/Finance/{UnbalancedEntryException,ClosedPeriodException}.php
app/Listeners/Finance/PostPayrollJournalEntryListener.php
database/seeders/DefaultChartOfAccountsSeeder.php
app/Filament/Finance/Resources/{ChartOfAccountsResource,JournalEntryResource,FiscalPeriodResource}.php
app/Filament/Finance/Pages/TrialBalancePage.php
database/factories/Finance/{AccountFactory,JournalEntryFactory}.php
tests/Feature/Finance/{LedgerPostingTest,PeriodLockTest,PayrollPostingTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_accounts`, `fin_journal_entries`, `fin_journal_lines`, `fin_fiscal_periods`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]). The GL is the ledger of record: every finance module posts to it via `LedgerService::post` (in-domain call, not a cross-domain write).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Consumes | `PayrollRunApproved` → journal entry via `PostPayrollJournalEntryListener` (retries on closed period) | [[../../hr/payroll/_module\|hr.payroll]] |
| Reads | nothing external | — |

## Entity Notes

- [[architecture]] — service write-path, immutability, money handling
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, period locking
- [[decisions]] — manual-CoA deviation
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/trial-balance]], [[features/fiscal-period-lock]]

## Related

- [[../invoicing/_module]]
- [[../expenses/_module]]
- [[../financial-reporting/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
