---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.cashflow
status: planned
priority: v1
depends-on: [finance.invoicing, finance.bank, core.billing, core.rbac, core.notifications]
soft-depends: [finance.ap, hr.payroll, finance.forecasting]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [fin_cashflow_projections, fin_cashflow_items]
permission-prefix: finance.cashflow
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Cash Flow

Cash flow forecasting and receivables-vs-payables timeline. Short-term liquidity planning.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/invoicing\|finance.invoicing]] | inflows from open invoices + due dates |
| Hard | [[domains/finance/bank-accounts\|finance.bank]] | opening cash position |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, low-cash alerts |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] | outflows from scheduled bills; manual outflow items without it |
| Soft | [[domains/hr/payroll\|hr.payroll]] | payroll outflow estimates |
| Soft | [[domains/finance/forecasting\|finance.forecasting]] | longer-horizon link |

---

## Core Features

- 13-week rolling cash flow projection (standard treasury horizon)
- Inflows: expected customer payments (from AR aging + due dates)
- Outflows: scheduled supplier payments (from AP), payroll, recurring expenses, manual items
- Opening + closing cash position per week
- Actual vs projected cash comparison
- Low-cash alerts: warn when projected balance falls below threshold (company setting *(assumed)*)
- Scenario toggles: best/worst case collection timing (shift inflows ± 2 weeks *(assumed)*)
- Bank balance integration (from Bank Accounts)

---

## Data Model

### fin_cashflow_projections

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| week_start | date | unique `(company_id, week_start, is_actual)` |
| opening_cents / inflow_cents / outflow_cents / closing_cents | bigint | |
| is_actual | boolean | actual rows backfilled weekly |

### fin_cashflow_items

| Column | Type | Notes |
|---|---|---|
| id, projection_id FK, company_id | ulid | |
| type | string | inflow / outflow |
| source | string | invoice / bill / payroll / manual |
| source_id | ulid nullable | |
| description | string | |
| amount_cents | bigint | |
| expected_date | date | |

---

## DTOs

### AddManualItemData — type (in:inflow,outflow), description, amount_cents (min:1), expected_date
### CashFlowProjectionData (output) — weeks[] (week_start, opening, inflow, outflow, closing, is_actual), threshold_breaches[]

## Services & Actions

- `CashFlowService::rebuild(): void` — regenerates 13 weeks from invoices (due dates), bills, payroll estimates + manual items; opening from bank balances
- `CashFlowService::projection(string $scenario = 'base'): CashFlowProjectionData`
- `AddManualItemAction::run(AddManualItemData $data): void`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RebuildCashFlowCommand` | finance | nightly 03:30 | full regenerate (delete+rebuild projected rows) — deterministic |
| `LowCashAlertCommand` | notifications | weekly Mon 08:00 | once per breach week (flag) |

---

## Filament

**Nav group:** Planning

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CashFlowPage` | #9 report custom page | 13-week grid + apex chart, scenario toggle, manual item add |
| Low-cash alert widget | #6 widget | next breach week |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('finance.cashflow.view-any') && BillingService::hasModule('finance.cashflow')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`finance.cashflow.view` · `finance.cashflow.manage-items`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Weekly closing = opening + inflows − outflows chained over 13 weeks (brick/money)
- [ ] Open invoice lands in week of due date; paid invoice drops out on rebuild
- [ ] AP outflows appear only when module active
- [ ] Low-cash alert fires once per breach week
- [ ] Scenario shift moves inflows, not outflows

---

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

---

## Related

- [[domains/finance/accounts-receivable]]
- [[domains/finance/accounts-payable]]
- [[domains/finance/forecasting]]
- [[domains/finance/bank-accounts]]
