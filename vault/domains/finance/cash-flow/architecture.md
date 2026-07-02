---
domain: finance
module: cash-flow
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cash Flow — Architecture

`CashFlowService` builds and serves the 13-week projection; `AddManualItemAction` handles ad-hoc manual items. The module reads from invoicing, bank accounts, AP, and payroll — it owns no source data beyond manual items.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw float math. Weekly chaining (`closing = opening + inflows − outflows`) is integer-cent arithmetic. See [[../../../architecture/packages]] (brick/money).

## Rebuild & projection logic

- `rebuild()` regenerates all 13 weeks: it deletes projected rows and rebuilds from open invoices (placed in the week of their due date), scheduled bills (AP), payroll estimates, recurring expenses, and manual items. Opening cash for week 1 comes from bank balances; each subsequent week's opening is the prior week's closing.
- A **full regenerate (delete + rebuild)** is deterministic — re-running produces the same projected rows. Actual rows (`is_actual = true`) are backfilled weekly and are not touched by the projected rebuild.
- `projection(scenario)` returns the assembled weeks plus any threshold breaches.
- AP-sourced outflows only appear when the AP module is active; otherwise outflows come from manual items.

## Scenario shift

Scenario toggles (best/worst case collection timing) shift **inflows only** by ± 2 weeks *(assumed)*; outflows are unaffected.

## Jobs & scheduling

- `RebuildCashFlowCommand` (finance queue, nightly 03:30): full deterministic regenerate.
- `LowCashAlertCommand` (notifications queue, weekly Mon 08:00): fires once per breach week (flagged so it does not re-alert), using core.notifications. Threshold is a company setting *(assumed)*.

See [[../../../architecture/queue-jobs]], [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]].
