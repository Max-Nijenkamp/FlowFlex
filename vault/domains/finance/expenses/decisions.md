---
domain: finance
module: expenses
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Expenses — Decisions

## Single approver in v1 (chain deferred)

The approval workflow is intended to ship as a single-approver step gated by `finance.expenses.approve`, rather than a full employee → manager → finance chain. The multi-step chain is a later hook *(assumed)*. See [[features/approval-workflow]].

## Submitter linkage: user always, employee when HR active

Every expense carries a `user_id` (the submitter). `employee_id` is nullable and populated only when [[../../hr/employee-profiles/_module|hr.profiles]] is active *(assumed: employee_id nullable → user_id always)*. Reimbursement via payroll only applies to employee-linked expenses.

## Policy is flag, not block

Over-limit expenses are flagged (`is_over_limit`) for reviewer attention, not blocked at submission *(assumed)*. See [[features/expense-policy]].

## Money precision

Amounts are integer minor units via `brick/money`, never floats — see the strip-to-shell context in [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

See [[unknowns]].
