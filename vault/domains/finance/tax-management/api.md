---
domain: finance
module: tax-management
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tax Management — DTOs, Services & Events

## DTOs

### CreateTaxRateData
| Field | Type | Validation |
|---|---|---|
| name | string | required |
| rate_basis_points | int | 0–10000 |
| type | string | in set (vat / gst / sales-tax) |
| jurisdiction | string | ISO country |
| is_reverse_charge | boolean | |

### TaxReturnData (output)
period, output_tax_cents, input_tax_cents, net_payable_cents, breakdown per rate.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

- `TaxCalculator::forLine(int $amountCents, TaxRate $rate): Money` — single tax-math entry point for all consuming modules.
- `TaxService::periodSummary(string $period): TaxReturnData` — sums invoice output tax + bill/expense input tax.
- `TaxService::filePeriod(string $period): void` — snapshot + status `filed` (locked).
- `ValidateVatNumberAction::run(string $vatNumber): bool` — VIES; network failure = "unverified", never blocks save *(assumed)*.

## Events

This module fires and consumes no events. Tax calculation is invoked by consuming modules (invoicing, AP, expenses) as direct in-domain service/helper calls — no events. See [[../../../architecture/event-bus]].

See [[security]], [[../financial-reporting/_module]].
