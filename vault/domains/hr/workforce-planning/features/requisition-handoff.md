---
domain: hr
module: workforce-planning
feature: requisition-handoff
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Requisition Handoff

## Purpose

Convert an approved planned role into a recruitment requisition when the Recruitment module is active.

## Behavior

- `ApprovePlannedRoleAction`: on approve, if `hr.recruitment` is active, create a requisition and store its id on `hr_planned_roles.requisition_id`.
- Without recruitment, the role status is tracked manually (soft-dep degraded).
- No cross-domain events; the handoff is a direct action-time integration.

## Tables / Permissions

- Table: `hr_planned_roles` (`requisition_id`) ([[../data-model]])
- Permission: `hr.workforce.approve-role`

## UI

- **Kind**: background (a page action, not a standalone screen)
- **Page**: no dedicated page — invoked as the "Approve" row action on the Planned Roles resource (`/hr/planned-roles`)
- **Layout**: no screen of its own; the outcome is the `requisition_id` populating on the planned-role row and the status moving to `approved`
- **Key interactions**: user runs Approve on a planned role → if `hr.recruitment` active, a requisition is created and its id stored; without recruitment, status is tracked manually
- **States**: empty = n/a (action, not a view) · loading = brief action spinner while the requisition is created · error = "Couldn't create requisition" toast, approval rolled back *(assumed)* · selected = post-approve, the role row shows the linked requisition id · degraded = recruitment inactive → no requisition, manual tracking only
- **Gating**: Approve action requires `hr.workforce.approve-role`

## Data

- Owns / writes: `hr_planned_roles` (`status`, `requisition_id`) — own module table
- Reads: `hr.recruitment` module-active flag; creates the requisition via `hr.recruitment` service, not by writing its tables
- Cross-domain writes: via `hr.recruitment` service call at action time only — never writes recruitment tables directly ([[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: hands off to `hr.recruitment` job-requisitions at approve time (direct service integration, **not** a fired event per spec) → recruitment creates a requisition; per index edge `workforce -.requisitions.-> recruitment`
- Shared entity: `hr_planned_roles.requisition_id` links to the recruitment requisition

## Related

- [[../_module]]
- [[../recruitment/_module]]
- [[planned-roles]]
