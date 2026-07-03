---
domain: finance
module: budgets
feature: budget-versioning
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Budget Versioning & Approval

Draft → approved workflow with in-year revisions kept as immutable versions.

- `BudgetResource` (#1 CRUD resource, [[../../../../architecture/ui-strategy]]): budget header (name, fiscal year, scope), line-editor grid per account/period, an approve action, and a version column.
- `approve(budgetId)` moves draft → approved; approved lines become immutable *(assumed)*.
- `revise(budgetId)` creates a new `version` row copying the lines and preserving the prior version — enforced by `unique (company_id, name, fiscal_year, version)`.
- `copyFromYear(fromYear, toYear)` bootstraps next year's budget from the prior year's lines as a starting point.
- All budgeted amounts are integer minor units (cents) via brick/money.

## UI
- **Kind**: simple-resource
- **Page**: `BudgetResource` under `/finance/budgets`
- **Layout**: budget header (name, fiscal year, scope) above a line-editor grid, one row per account/period; version column; approve action in the header.
- **Key interactions**: edit budget lines in-grid, `approve` (draft → approved, lines become immutable), `revise` (spawns a new immutable version), `copyFromYear` (bootstrap next year).
- **States**: empty (no budget → prompt to create or copy from prior year) · loading (grid skeleton) · error (validation on line edit) · selected (active version row highlighted; approved versions read-only)
- **Gating**: `finance.budgets.manage` (create/edit/revise), `finance.budgets.approve` (approve) — *(assumed)*

## Data
- Owns / writes: `fin_budgets`, `fin_budget_lines` only. All amounts integer minor units (cents) via brick/money. Approved lines immutable *(assumed)*; `revise()` writes a new immutable version rather than mutating.
- Reads: `fin_accounts` (finance.ledger) read-only for the account picker.
- Cross-domain writes: none — no cross-domain events fired. Never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no events.
- Feeds: no events. In-domain service calls only (`approve`, `revise`, `copyFromYear`); read by [[variance-tracking]] and finance.forecasting for comparison columns.

## Test Checklist

### Unit
- [ ] `copyFromYear` copies every prior-year line into the new fiscal year at the same account/period, amounts unchanged (integer cents)
- [ ] Version increments monotonically; approved-line-immutability rule rejects a mutation on an approved version *(assumed)*

### Feature (Pest)
- [ ] `approve(budgetId)` moves draft → approved and subsequent line edits are rejected (approved lines immutable)
- [ ] `revise(budgetId)` creates a new `version` row copying the lines and preserves the prior version; `unique (company_id, name, fiscal_year, version)` holds
- [ ] Tenant isolation: company A cannot approve/revise company B budgets; concurrent double-approve rejected via row lock ([[../architecture]] Concurrency)

### Livewire
- [ ] Approve header action visible only with `finance.budgets.approve`; hidden/denied otherwise
- [ ] Line-editor grid is read-only on an approved version; edit permitted only on the active draft

See [[../architecture]], [[../api]], [[../security]], [[variance-tracking]].
