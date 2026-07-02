---
domain: finance
module: tax-management
feature: tax-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Tax Period Report & VAT Return

Period summary of output vs input tax, plus VAT return preparation and filing.

- `TaxReturnPage` (#9 report custom page, [[../../../../architecture/ui-strategy]]): per-period VAT return prep with a file action.
- Backed by `TaxService::periodSummary(period): TaxReturnData` — sums invoice output tax + bill/expense input tax; `output − input = net payable`.
- `TaxService::filePeriod(period)` snapshots the period (`output_tax_cents`, `input_tax_cents`, `net_payable_cents`) and sets status `filed`, locking it against rate-affecting recomputation. Permission-gated by `finance.tax.file-period`.
- OSS reporting is a summary only — no OSS filing integration in v1 *(assumed)*.
- `fin_tax_periods` keys on `period` (`YYYY-Qn` or `YYYY-MM`), unique per company.

## UI
- **Kind**: custom-page (report)
- **Page**: `TaxReturnPage` — `/finance/tax/return`
- **Layout**: per-period VAT return prep — period selector with an output/input/net summary and a file action; OSS summary section.
- **Key interactions**: pick a period; review output − input = net payable; file the period (snapshots + locks it).
- **States**: empty (no data for period) · loading (summary compute) · error (already filed / recompute blocked) · selected (period under review).
- **Gating**: `finance.tax.file-period`.

## Data
- Owns / writes: `fin_tax_periods` — snapshots `output_tax_cents`, `input_tax_cents`, `net_payable_cents` (integer minor units via brick/money) and sets status `filed`, locking against rate-affecting recomputation.
- Reads: invoice output tax + bill/expense input tax from finance.invoicing / finance.ap / finance.expenses (read-only); each of those owns its own line tax amounts.
- Cross-domain writes: none — only snapshots into own `fin_tax_periods`, never touches other domains' tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no cross-domain events; pulls period totals by reading the owning modules' tax data.
- Feeds: no events — filing is own-domain state only. OSS summary only, no filing integration in v1 *(assumed)*.

See [[../api]], [[../security]], [[../data-model]].
