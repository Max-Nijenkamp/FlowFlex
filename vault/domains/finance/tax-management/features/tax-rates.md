---
domain: finance
module: tax-management
feature: tax-rates
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Tax Rates & Classes

Configuration of the rates and classes that drive line-level tax math across consuming modules.

- `TaxRateResource` (#1 CRUD resource, [[../../../../architecture/ui-strategy]]) manages rates and classes.
- Rate records: name, `rate_basis_points` (integer, e.g. `2100` = 21%), type (vat / gst / sales-tax), jurisdiction (ISO country), reverse-charge flag, active flag.
- Tax classes (standard / reduced / zero / exempt) each map to a `default_rate_id`.
- Reverse-charge rates yield zero tax and carry a flag to the invoice/bill line plus a ledger note.
- `TaxCalculator::forLine(amountCents, rate): Money` is the single tax-math entry point; rounding is line-level and consistent with invoicing (brick/money).
- Rates referenced by lines are never hard-deleted (soft-delete only).

## UI
- **Kind**: simple-resource
- **Page**: `TaxRateResource` (+ tax classes) — `/finance/tax/rates`
- **Layout**: rate table (name, `rate_basis_points`, type, jurisdiction, reverse-charge/active flags) + tax-class management mapping each class to a `default_rate_id`.
- **Key interactions**: create/edit rates and classes; toggle active; set reverse-charge; soft-delete referenced rates.
- **States**: empty (no rates) · loading (list/form) · error (validation) · selected (rate/class being edited).
- **Gating**: `finance.tax.manage-rates` *(assumed)*.

## Data
- Owns / writes: `fin_tax_rates`, `fin_tax_classes` (`rate_basis_points` = integer; tax math via `TaxCalculator::forLine` using brick/money, line-level rounding). Rates referenced by lines are soft-deleted only.
- Reads: own tables; `TaxCalculator::forLine(amountCents, rate): Money` is the single tax-math entry point.
- Cross-domain writes: none — finance.invoicing, finance.ap, and finance.expenses read these rates but each owns its own line tax amounts ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no cross-domain events.
- Feeds: no events — consumed by read: invoicing / AP / expenses call `TaxCalculator::forLine` and store their own line tax; this feature only supplies rate config.

See [[../api]], [[../data-model]], [[../architecture]].
