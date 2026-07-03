---
domain: finance
module: cash-flow
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Planning *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CashFlowPage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — 13-week grid (opening/inflow/outflow/closing) + apex chart, scenario toggle, inline manual-item add; realtime none | `/finance/cash-flow` |
| `AddManualItemAction` | action on `CashFlowPage` | inline modal action — adds/edits a manual inflow/outflow item; own permission `finance.cashflow.manage-items` + `panel-action` rate limiter (mutates money) | visible only with `manage-items` |
| `LowCashAlertWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | surfaces the next breach week; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.cashflow.view-any') && BillingService::hasModule('finance.cashflow')`
per [[../../../architecture/filament-patterns]] #1. `CashFlowPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages; the `LowCashAlertWidget` states it too. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Manual item add/edit (`AddManualItemAction`, `fin_cashflow_items` CRUD) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Nightly rebuild (`RebuildCashFlowCommand`, delete + regenerate projected rows) | n-a | single-writer scheduled job, full deterministic regenerate — no concurrent editors of projected rows |
| Weekly actual-row backfill (`is_actual` rows) | n-a | single-writer scheduled append; projected rebuild never touches actual rows |
| `projection(scenario)` / weekly chaining | n-a | read-only derived computation over projected + actual rows — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/queue-jobs]], [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]].
