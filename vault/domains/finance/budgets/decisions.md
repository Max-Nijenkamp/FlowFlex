---
domain: finance
module: budgets
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Budgets — Decisions

## Revisions as new versions, not in-place edits

In-year budget changes create a new `version` row (copying lines) rather than mutating the approved budget. Rationale: preserves the approved baseline for audit and variance history; `unique (company_id, name, fiscal_year, version)` enforces the sequence.

## Approved lines immutable

Once a budget is approved, its lines are locked; further change goes through `revise()` *(assumed)*. Overridable via ADR if unrestricted editing of approved budgets is required.

## Actuals imported from the ledger, not stored

Budgets store only budgeted figures; actuals are summed live from journal lines at variance time (cached per closed period). The budget module owns no actuals table.

## Variance threshold default 10%

The variance-alert threshold defaults to 10% *(assumed)*, surfaced as a company setting.

See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]], [[unknowns]].
