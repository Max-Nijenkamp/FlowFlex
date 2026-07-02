---
domain: hr
module: workforce-planning
feature: planned-roles
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Planned Roles

## Purpose

Hire forecast: planned new roles under a headcount plan, with target start dates and budgeted cost. Open role pipeline.

## Behavior

- Each role belongs to a plan; carries `title`, `target_start_date`, `budgeted_salary_cents`.
- Status lifecycle: `planned` → `approved` → `filled`.
- `ApprovePlannedRoleAction` moves to approved; `MarkRoleFilledAction` to filled.
- CRUD + approve action via `PlannedRoleResource` (#1 CRUD resource).

## Tables / Permissions

- Table: `hr_planned_roles` ([[../data-model]])
- Permissions: `hr.workforce.view-any`, `hr.workforce.create`, `hr.workforce.update`, `hr.workforce.approve-role`

## UI

- **Kind**: simple-resource
- **Page**: "Planned Roles" (`/hr/planned-roles`)
- **Layout**: Filament `PlannedRoleResource` table (title, parent plan, target start date, budgeted salary, status badge planned/approved/filled) with create/edit form and row actions; status shown as a coloured badge
- **Key interactions**: create a role under a plan; run the Approve action (→ approved, triggers requisition handoff if recruitment active); run Mark Filled action (→ filled)
- **States**: empty = "No planned roles" placeholder · loading = table skeleton · error = action failure toast · selected = row opens edit form; status badge reflects lifecycle stage
- **Gating**: visible with `hr.workforce.view-any`; create requires `hr.workforce.create`; edit requires `hr.workforce.update`; Approve action requires `hr.workforce.approve-role`

## Data

- Owns / writes: `hr_planned_roles`
- Reads: parent `hr_headcount_plans` (own module); department reference from `hr.profiles` *(assumed)*
- Cross-domain writes: none directly — requisition creation on approve is via `hr.recruitment` service, not a table write ([[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: on approve, `ApprovePlannedRoleAction` hands off to `hr.recruitment` (creates a requisition) — see [[requisition-handoff]]
- Shared entity: `hr_headcount_plans` (own parent record)

## Related

- [[../_module]]
- [[requisition-handoff]]
