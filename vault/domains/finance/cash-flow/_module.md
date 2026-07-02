---
domain: finance
module: cash-flow
type: module
module-key: finance.cashflow
priority: v1
build-status: planned
status: wip
depends-on: [finance.invoicing, finance.bank, core.billing, core.rbac, core.notifications]
soft-depends: [finance.ap, hr.payroll, finance.forecasting]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [fin_cashflow_projections, fin_cashflow_items]
permission-prefix: finance.cashflow
encrypted-fields: []
color: "#4ADE80"
updated: 2026-06-20
---

# Cash Flow

Cash flow forecasting and a receivables-vs-payables timeline for short-term liquidity planning. A 13-week rolling projection is intended to be rebuilt nightly from invoices, bills, payroll, and manual items, with low-cash alerts.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

The module projects weekly cash position over a standard 13-week treasury horizon. Inflows come from open invoices (by due date), outflows from scheduled supplier bills, payroll estimates, recurring expenses, and manual items; opening cash comes from bank balances. The projection is intended to be a full nightly regenerate (delete + rebuild projected rows) so it is deterministic, with a weekly low-cash alert when a projected balance breaches a threshold.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../invoicing/_module\|finance.invoicing]] | inflows from open invoices + due dates |
| Hard | [[../bank-accounts/_module\|finance.bank]] | opening cash position |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, low-cash alerts |
| Soft | [[../accounts-payable/_module\|finance.ap]] | outflows from scheduled bills; manual outflow items without it |
| Soft | [[../../hr/payroll/_module\|hr.payroll]] | payroll outflow estimates |
| Soft | [[../forecasting/_module\|finance.forecasting]] | longer-horizon link |

## Core Features

- 13-week rolling cash flow projection (standard treasury horizon).
- Inflows: expected customer payments (from AR aging + due dates).
- Outflows: scheduled supplier payments (from AP), payroll, recurring expenses, manual items.
- Opening + closing cash position per week.
- Actual vs projected cash comparison.
- Low-cash alerts: warn when projected balance falls below threshold (company setting) *(assumed)*.
- Scenario toggles: best/worst case collection timing (shift inflows ± 2 weeks) *(assumed)*.
- Bank balance integration (from Bank Accounts).

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RebuildCashFlowCommand` | finance | nightly 03:30 | full regenerate (delete + rebuild projected rows) — deterministic |
| `LowCashAlertCommand` | notifications | weekly Mon 08:00 | once per breach week (flag) |

See [[../../../architecture/queue-jobs]].

## Permissions

`finance.cashflow.view` · `finance.cashflow.manage-items`

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Weekly closing = opening + inflows − outflows chained over 13 weeks (brick/money)
- [ ] Open invoice lands in week of due date; paid invoice drops out on rebuild
- [ ] AP outflows appear only when module active
- [ ] Low-cash alert fires once per breach week
- [ ] Scenario shift moves inflows, not outflows

## Build Manifest

```
database/migrations/xxxx_create_fin_cashflow_projections_table.php
database/migrations/xxxx_create_fin_cashflow_items_table.php
app/Models/Finance/{CashflowProjection,CashflowItem}.php
app/Data/Finance/{AddManualItemData,CashFlowProjectionData}.php
app/Services/Finance/CashFlowService.php
app/Actions/Finance/AddManualItemAction.php
app/Console/Commands/Finance/{RebuildCashFlowCommand,LowCashAlertCommand}.php
app/Filament/Finance/Pages/CashFlowPage.php
app/Filament/Finance/Widgets/LowCashAlertWidget.php
database/factories/Finance/CashflowProjectionFactory.php
tests/Feature/Finance/CashFlowProjectionTest.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_cashflow_projections`, `fin_cashflow_items`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Consumes | `InvoicePaid` effect (paid invoices drop on rebuild) | [[../invoicing/_module\|finance.invoicing]] |
| Reads | open invoices (read-only) | [[../invoicing/_module\|finance.invoicing]] |
| Reads | bank balances, AP bills (read-only) | [[../bank-accounts/_module\|finance.bank]], [[../accounts-payable/_module\|finance.ap]] |
| Reads | payroll estimates (read-only) | [[../../hr/payroll/_module\|hr.payroll]] |

## Entity Notes

- [[architecture]] — rebuild logic, projection chaining, scenario shift, scheduling
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions
- [[decisions]] — full-regenerate strategy
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/cash-flow-projection]]

## Related

- [[../accounts-receivable/_module]]
- [[../accounts-payable/_module]]
- [[../forecasting/_module]]
- [[../bank-accounts/_module]]
- [[../financial-reporting/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
