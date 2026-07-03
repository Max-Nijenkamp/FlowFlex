---
domain: finance
module: cash-flow
feature: cash-flow-projection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — 13-Week Cash Flow Projection

The core treasury view: a 13-week rolling grid of opening/inflow/outflow/closing cash, rebuilt nightly.

- `CashFlowPage` (#9 report custom page, [[../../../../architecture/ui-strategy]]): 13-week grid + apex chart, scenario toggle, and inline manual-item add.
- `LowCashAlertWidget` (#6 widget): surfaces the next breach week.
- Weeks chain: `closing = opening + inflows − outflows`; week 1 opening comes from bank balances, each later week opens at the prior week's closing (brick/money, integer cents).
- `CashFlowService::rebuild()` runs nightly (03:30, finance queue) as a full delete + regenerate of projected rows: open invoices land in the week of their due date, paid invoices drop out, AP bills/payroll/recurring expenses/manual items add outflows.
- `CashFlowService::projection(scenario)` assembles the weeks and threshold breaches; scenario toggles shift inflows ± 2 weeks, outflows unchanged.
- Actual-vs-projected comparison uses the weekly-backfilled `is_actual` rows.

## UI
- **Kind**: custom-page (report) + widget + background
- **Page**: `CashFlowPage` under `/finance/cash-flow`; `LowCashAlertWidget` on the finance dashboard.
- **Layout**: 13-week rolling grid (opening/inflow/outflow/closing) + apex chart, scenario toggle, inline manual-item add. Widget surfaces the next breach week.
- **Key interactions**: toggle scenario (shifts inflows ± 2 weeks), add a manual cash item inline, read weekly breach flags.
- **States**: empty (no projection yet → prompt to run rebuild) · loading (grid + chart skeleton) · error (rebuild failed / stale) · selected (breach week highlighted, scenario active)
- **Gating**: `finance.cashflow.view-any`

## Data
- Owns / writes: `fin_cashflow_projections`, `fin_cashflow_items` only. `CashFlowService::rebuild()` runs nightly (03:30, finance queue) as a full delete + regenerate. All amounts integer minor units via brick/money.
- Reads (all read-only): open invoices/due dates from finance.invoicing, bank balances from finance.bank, AP bills from finance.ap, payroll from hr.payroll.
- Cross-domain writes: none. Never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: `InvoicePaid` (finance.invoicing) — effect is indirect: paid invoices simply drop out of the next `rebuild()` (no direct write on the event).
- Feeds: `LowCashAlertWidget` breach notifications. In-domain service calls (`rebuild`, `projection`).

## Test Checklist

### Unit
- [ ] Weekly chaining: `closing = opening + inflows − outflows`, week 1 opening from bank balance, each later week opens at prior closing (brick/money, integer cents)
- [ ] Scenario shift moves inflows ± 2 weeks only; outflows unchanged
- [ ] Threshold-breach detection flags a week only when projected balance falls below the low-cash threshold

### Feature (Pest)
- [ ] `rebuild()` full delete + regenerate is deterministic: open invoice lands in the week of its due date, paid invoice drops out, AP bills/payroll/manual items add outflows; re-run yields identical projected rows
- [ ] AP-sourced outflows appear only when `finance.ap` active; otherwise outflows come from manual items
- [ ] `LowCashAlertCommand` fires once per breach week (flag guard suppresses re-alert on second run); tenant isolation on source reads (company A cannot read company B invoices/bank balances)

### Livewire
- [ ] `CashFlowPage` renders the 13-week grid + chart and toggles scenario; `canAccess` denied without `finance.cashflow.view-any`
- [ ] `AddManualItemAction` validates type/amount/date and appends a manual item; blocked without `finance.cashflow.manage-items`

See [[../architecture]], [[../api]], [[../data-model]].
