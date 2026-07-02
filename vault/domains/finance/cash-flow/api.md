---
domain: finance
module: cash-flow
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cash Flow — DTOs, Services & Events

## DTOs

### AddManualItemData
| Field | Type | Validation |
|---|---|---|
| type | string | in:inflow,outflow |
| description | string | required |
| amount_cents | int | min:1 |
| expected_date | date | required |

### CashFlowProjectionData (output)
- `weeks[]` — each: `week_start`, `opening`, `inflow`, `outflow`, `closing`, `is_actual`.
- `threshold_breaches[]` — weeks whose projected balance falls below the low-cash threshold.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

`CashFlowService`:

- `rebuild(): void` — regenerates 13 weeks from invoices (due dates), bills, payroll estimates + manual items; opening from bank balances. Full delete + rebuild of projected rows.
- `projection(string $scenario = 'base'): CashFlowProjectionData` — assembled weeks + threshold breaches; scenario shifts inflows only.

`AddManualItemAction` (lorisleiva/laravel-actions per [[../../../architecture/patterns/interface-service]]):

- `run(AddManualItemData $data): void` — appends a manual inflow/outflow item.

## Events

This module fires and consumes no cross-domain events. It pulls source data by reading the relevant tables/services directly within the finance domain; the only cross-module dependency carrying signal is core.notifications for the low-cash alert (`LowCashAlertCommand`).

See [[security]], [[features/cash-flow-projection]], [[../forecasting/_module]].
