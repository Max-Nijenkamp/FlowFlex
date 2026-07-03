---
domain: finance
module: budgets
feature: variance-tracking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Budget vs Actual Variance

Live variance of actuals (from the ledger) against budgeted figures, with over-budget alerts.

- `BudgetVariancePage` (#9 report custom page, [[../../../../architecture/ui-strategy]]): per account/period grid of budgeted / actual / variance / variance %, with over-budget highlighting and drill-down into contributing journal entries.
- `BudgetVarianceWidget` (#6 widget): surfaces accounts currently over budget.
- `variance(budgetId, period)` sums journal lines per account/period (actuals) and computes `variance_cents = actual_cents − budgeted_cents` (brick/money, integer cents), then `variance_percent`.
- `BudgetVarianceAlertCommand` (notifications, monthly 2nd 08:00) notifies once per (budget, period) breaching the threshold (default 10% *(assumed)*), flagged to avoid re-alerting.
- Results cache per closed period (`company:{id}:finance:budget-variance:{budget}:{period}`, 1 h), busted by journal posting in-period or budget edits.

## UI
- **Kind**: custom-page (report) + widget + background
- **Page**: `BudgetVariancePage` under `/finance/budgets/variance`; `BudgetVarianceWidget` on the finance dashboard.
- **Layout**: per account/period grid of budgeted / actual / variance / variance %, over-budget rows highlighted; drilldown lists contributing journal entries. Widget surfaces the current over-budget accounts.
- **Key interactions**: pick budget + period, sort by variance, click a line to drill down to journal entries.
- **States**: empty (no approved budget for period) · loading (grid + widget skeleton) · error (missing budget/period) · selected (drilldown row expanded, over-budget highlighted)
- **Gating**: `finance.budgets.view-any`

## Data
- Owns / writes: nothing new — reads `fin_budget_lines` (own module). `variance_cents`/`variance_percent` computed in integer minor units via brick/money.
- Reads: actuals = journal lines from finance.ledger (`fin_journal_*`), read-only. Results cached per closed period (1 h), busted on in-period posting or budget edit.
- Cross-domain writes: none. Never writes ledger or any other domain's tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: reacts to in-period journal posting only as a cache-bust signal (no event payload written back).
- Feeds: `BudgetVarianceAlertCommand` emits notifications (monthly, 2nd 08:00, once per budget/period, threshold 10% *(assumed)*). In-domain service calls to compute variance.

## Test Checklist

### Unit
- [ ] `variance_cents = actual_cents − budgeted_cents` per account/period (brick/money, integer cents); `variance_percent` derived last
- [ ] Threshold-breach detection flags a period only when variance % exceeds the configured threshold (default 10% *(assumed)*)

### Feature (Pest)
- [ ] `variance(budgetId, period)` sums ledger journal lines per account/period against budgeted lines from GL fixtures
- [ ] `BudgetVarianceAlertCommand` notifies once per (budget, period) over threshold; the flag guard suppresses re-alerting on a second run
- [ ] Variance cache busts on in-period journal posting or a budget edit; tenant isolation on the ledger read (company A cannot read company B actuals)

### Livewire
- [ ] `BudgetVariancePage` renders the budgeted/actual/variance grid and drills down to contributing journal entries; `canAccess` denied without `finance.budgets.view-any`
- [ ] `BudgetVarianceWidget` surfaces only over-budget accounts for the tenant

See [[../architecture]], [[../api]], [[../data-model]], [[budget-versioning]].
