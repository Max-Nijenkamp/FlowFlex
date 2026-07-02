---
domain: hr
module: leave-management
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leave Management — Unknowns & Assumptions

Every `*(assumed)*` item from the spec plus open questions. Resolve via ADR before relying on any as a hard default.

## Open Questions

- Multi-level chain depth: v1 intends single-level (direct manager) with a config hook for chains *(assumed)* — extend when a customer needs >1 level.
- Half-day requests: intended to be supported via decimal `days_requested`; UI to expose a full/half-day toggle *(assumed)*.

## Assumptions (carried from spec)

- Without self-service (`hr.self-service`), HR staff submit requests on behalf of employees *(assumed)*.
- `hr_leave_types.name` is unique per company *(assumed)*.
- `hr_leave_types.color` defaults to `#4ADE80` *(assumed)*.
- `(company_id, name)` unique index on `hr_leave_types` *(assumed)*.
- `hr_leave_requests.rejection_reason` column existence/shape *(assumed)*.
- `approved → cancelled` allowed only before `start_date` *(assumed)*.
- `SubmitLeaveRequestData.note` max length 1000 *(assumed)*.
- Requested span must yield ≥ 0.5 working days *(assumed)*.
- `OverlappingLeaveException` thrown only when the leave type forbids overlap *(assumed)*.
- `PendingApprovalsWidget` counts pending for the current approver *(assumed)*.
- `CarryOverLeaveBalancesCommand` skips rows already carried (`allocated_days` includes a carry marker) *(assumed)*.

## Related

- [[_module]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
