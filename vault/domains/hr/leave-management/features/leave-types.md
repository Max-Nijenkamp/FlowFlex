---
domain: hr
module: leave-management
feature: leave-types
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Leave Types

## Purpose

Per-company configurable leave categories (annual, sick, parental, unpaid, custom) that drive accrual, carry-over, and whether a request needs approval.

## Behavior

- Admin-configurable: accrual days/year, carry-over days, `requires_approval` flag, display color.
- `requires_approval = false` types auto-approve on submit (skip the approval step).
- `accrual_days_per_year = 0` means no accrual (e.g. unpaid).

## UI

- **Kind**: simple-resource
- **Page**: "Leave Types" (`/hr/leave-types`)
- **Layout**: standard Filament table (name, color swatch, accrual/year, carry-over, requires-approval badge) with a create/edit form; admin config screen.
- **Key interactions**: create/edit a leave type; set accrual days, carry-over cap, `requires_approval` toggle, and display color.
- **States**: empty ("No leave types yet — create your first" primary CTA) · loading (table skeleton) · error (inline banner, retry) · selected (row opens edit form).
- **Gating**: visible with `hr.leave.manage-types`; create/edit/delete requires `hr.leave.manage-types`.

## Data

- Owns / writes: `hr_leave_types`
- Reads: own table only
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none *(reference data read by [[leave-balances]], [[leave-request-workflow]], [[accrual-jobs]] within this module)*
- Shared entity: none

## Related

- Table: `hr_leave_types` (see [[../data-model]])
- Permission: `hr.leave.manage-types` (see [[../security]])
- UI: `LeaveTypeResource` (#1 CRUD resource — admin config)
- Tests: `requires_approval = false` type auto-approves on submit
- Back to [[../_module]]
