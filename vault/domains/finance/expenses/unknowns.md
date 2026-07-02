---
domain: finance
module: expenses
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Expenses — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Approval chain** — v1 is a single approver (`finance.expenses.approve`); the employee → manager → finance chain is a later hook. *(assumed)*
- **Submitter linkage** — `employee_id` is nullable; `user_id` is always set (employee link only when HR active). *(assumed)*
- **Policy enforcement** — over-limit expenses are flagged, not blocked. *(assumed)*
- **Receipt requirement** — receipt is required per a per-category flag (configurable per category). *(assumed)*

UNVERIFIED: the concrete max receipt size default is "per settings" but no specific number is given in the spec. UNVERIFIED: the GL account pairing for the reimbursable liability (which liability account) is described narratively (expense account / reimbursable liability) but not enumerated.
