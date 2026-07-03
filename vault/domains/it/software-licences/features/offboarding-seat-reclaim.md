---
domain: it
module: software-licences
feature: offboarding-seat-reclaim
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Offboarding Seat Reclaim

When an employee is offboarded, flag all of their active licence seats for reclamation so admins can free the spend.

## Behaviour

- Consumes `EmployeeOffboarded` from hr.employee-profiles.
- `FlagSeatsForReclaimListener` (`ShouldQueue` + `WithCompanyContext`) sets `reclaim_flagged_at` on every active (`revoked_at IS NULL`) `it_licence_assignments` row for that `employee_id` within the event's `company_id`.
- Flagging is advisory: the seat stays active until an admin revokes it via [[seat-assignment]]. Flagged seats surface in [[renewal-alerts|LicenceRenewalWidget]].
- Writes only its own `it_licence_assignments` rows — never HR tables.

## UI

- **Kind**: background — queued event listener, no page ([[../../../../architecture/ui-strategy]]).
- **Page**: none.
- **Trigger**: `EmployeeOffboarded` (hr.employee-profiles) → `FlagSeatsForReclaimListener` flags that employee's active seats.

## Data

- Owns / writes: `it_licence_assignments` (`reclaim_flagged_at`) only.
- Reads: the `EmployeeOffboarded` payload (`company_id`, `employee_id` scalars); no HR table access.
- Cross-domain writes: none — the reclaim flag is written to this module's own table via the consumed event, never into HR's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeOffboarded` from [[../../../hr/employee-profiles/_module|hr.employee-profiles]] → flag that employee's active seats for reclaim.
- Feeds: nothing.
- Shared entity: `hr_employees` owned by hr.employee-profiles; referenced by `employee_id` scalar only.

## Test Checklist

### Unit
- [ ] Only active (`revoked_at IS NULL`) seats of the offboarded employee are selected for flagging

### Feature (Pest)
- [ ] `EmployeeOffboarded` → active seats get `reclaim_flagged_at`; seats stay active until an admin revokes
- [ ] Listener runs under `WithCompanyContext`; flags only the event company's assignments, never HR tables
- [ ] Re-delivery is idempotent — already-flagged seats unchanged

## Unknowns

- `*(assumed)*` reclaim is advisory (flag), not auto-revoke — see [[../unknowns|software-licences.unknowns]].

## Related

- [[../_module|Software Licences]] · [[seat-assignment]] · [[renewal-alerts]] · [[../../../hr/employee-profiles/_module|hr.employee-profiles]] · [[../../../../architecture/event-bus]]
