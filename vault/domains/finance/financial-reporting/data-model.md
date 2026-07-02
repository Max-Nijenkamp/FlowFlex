---
domain: finance
module: financial-reporting
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Financial Reporting — Data Model

**This module owns no tables.** Every statement is generated at request time from the general ledger. All monetary math is integer **minor units** (cents) via `brick/money`. Tenancy is inherited from the source tables' `company_id` scope per [[../../../security/tenancy-isolation]].

## Source tables (owned by General Ledger)

| Table | Used for |
|---|---|
| `fin_accounts` | account `type` + code ranges drive statement section mapping |
| `fin_journal_entries` | posted entries, period selection |
| `fin_journal_lines` | debit/credit amounts summed into statement lines (integer cents) |

Budgeted comparison figures are read from `fin_budget_lines` (budgets module) when active.

## ERD

```mermaid
erDiagram
    fin_accounts ||--o{ fin_journal_lines : "classifies"
    fin_journal_entries ||--o{ fin_journal_lines : "contains"
    fin_journal_lines }o--|| fin_budget_lines : "compared to (soft)"
```

No migrations ship with this module; the Build Manifest contains only DTOs, service, pages, and tests.

See [[architecture]], [[../general-ledger/data-model]], [[../budgets/data-model]].
