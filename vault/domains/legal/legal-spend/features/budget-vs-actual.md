---
domain: legal
module: legal-spend
feature: budget-vs-actual
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Budget vs Actual

Set budgets per matter or per period; compare against approved actuals with an over-budget flag and vendor breakdown.

## Behaviour

- Budget rows keyed `(company_id, matter_id?, period)`; null matter = period budget.
- Variance = approved actual − budget; over-budget flagged.
- Vendor breakdown: which firms cost most.
- Reports by matter, vendor, category, period.

## UI

- **Kind**: custom-page (dashboard)
- **Page**: `LegalSpendDashboardPage` (`/legal/spend/dashboard`).
- **Layout**: KPI row (total spend, budget, variance); budget-vs-actual bars per matter/period; vendor breakdown chart (apex); over-budget items highlighted red; budget edit inline/side.
- **Key interactions**: switch period; drill matter → matter spend; set/edit budget; export report.
- **States**: empty ("Set a budget to see variance") · loading (chart skeletons) · error (toast + retry) · selected (matter drilled, over-budget rows flagged).
- **Gating**: view `legal.spend.view-any`; edit budgets `legal.spend.manage-budgets`.

## Data

- Owns / writes: `legal_budgets`.
- Reads: own `legal_expenses` (approved) via `LegalSpendService`; matter names via legal.matters (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: variance surfaced on matter spend summaries.
- Shared entity: `legal_matters` (owned by legal.matters).

## Unknowns

- `*(assumed)*` single-currency variance — [[../unknowns]].

## Related

- [[../_module|Legal Spend]] · [[./expense-records]] · [[./invoice-approval]]
