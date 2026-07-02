---
domain: finance
module: budgets
type: module
module-key: finance.budgets
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.notifications]
soft-depends: [finance.forecasting, procurement.requisitions, hr.workforce]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [fin_budgets, fin_budget_lines]
permission-prefix: finance.budgets
encrypted-fields: []
color: "#4ADE80"
updated: 2026-06-20
---

# Budgets

Department and company-level budgets with actual-vs-budget variance tracking. Absorbed from the former FP&A domain. Budgets carry per-account, per-period lines; actuals are imported from the general ledger for variance reporting and alerts.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

The module records budgets scoped to company, department, or project, broken into monthly lines per GL account. Actuals are summed from journal lines to produce budget-vs-actual variance (absolute and %), with monthly alerts when actuals breach a threshold. Budgets move through a draft → approved workflow, can be copied from the prior year, and support in-year revisions as new versions. `remaining()` is exposed for procurement/workforce budget checks.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | actuals from journal lines per account |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, variance alerts |
| Soft | [[../forecasting/_module\|finance.forecasting]] | rolling forecast view |
| Soft | [[../../procurement/requisitions/_module\|procurement.requisitions]], [[../../hr/workforce-planning/_module\|hr.workforce]] | consume budget checks via `BudgetService::remaining()` |

## Core Features

- Budget record: name, fiscal year, scope (company / department / project).
- Budget lines: per GL account, per period (monthly breakdown).
- Import actuals from General Ledger automatically.
- Variance report: budget vs actual, absolute and %.
- Variance alerts: notify when actual exceeds budget by threshold (default 10% *(assumed)*).
- Budget approval workflow (draft → approved, `finance.budgets.approve`).
- Copy budget from previous year as starting point.
- Budget versions (revisions during the year — new version row, old kept).
- Rolling forecast view (links Forecasting).

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `BudgetVarianceAlertCommand` | notifications | monthly, 2nd 08:00 | once per (budget, period) over threshold (flag column) |

See [[../../../architecture/queue-jobs]].

## Permissions

`finance.budgets.view-any` · `finance.budgets.create` · `finance.budgets.update` · `finance.budgets.approve`

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Variance = actual − budget per account/period from GL fixtures (brick/money)
- [ ] Revision creates new version, preserves old
- [ ] Approved budget lines immutable (revise instead) *(assumed)*
- [ ] Alert fires once per period over threshold
- [ ] `remaining()` correct for partially consumed account/period

## Build Manifest

```
database/migrations/xxxx_create_fin_budgets_table.php
database/migrations/xxxx_create_fin_budget_lines_table.php
app/Models/Finance/{Budget,BudgetLine}.php
app/Data/Finance/{CreateBudgetData,BudgetData,BudgetVarianceData}.php
app/Contracts/Finance/BudgetServiceInterface.php
app/Services/Finance/BudgetService.php
app/Console/Commands/Finance/BudgetVarianceAlertCommand.php
app/Filament/Finance/Resources/BudgetResource.php
app/Filament/Finance/Pages/BudgetVariancePage.php
app/Filament/Finance/Widgets/BudgetVarianceWidget.php
database/factories/Finance/{BudgetFactory,BudgetLineFactory}.php
tests/Feature/Finance/{BudgetVarianceTest,BudgetVersionTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_budgets`, `fin_budget_lines`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Reads | `fin_journal_lines` + `fin_accounts` (read-only) for actuals/account picker | [[../general-ledger/_module\|finance.ledger]] |
| Reads by | `fin_budget_lines` read for budget comparison | [[../forecasting/_module\|finance.forecasting]] |

## Entity Notes

- [[architecture]] — variance computation, versioning, remaining(), alerts, caching
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions
- [[decisions]] — versioning + approval-immutability strategy
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/variance-tracking]], [[features/budget-versioning]]

## Related

- [[../general-ledger/_module]]
- [[../forecasting/_module]]
- [[../financial-reporting/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
