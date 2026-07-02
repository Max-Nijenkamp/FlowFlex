---
domain: it
module: access-provisioning
feature: deprovisioning-on-offboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# De-provisioning on Offboard

When HR offboards an employee, every live access grant is flagged for revocation so IT can pull access and
nothing is silently left open.

- Consumes `EmployeeOffboarded` from [[../../../hr/employee-profiles/_module|hr.profiles]] via `DeprovisionOnOffboardListener` (queued + `WithCompanyContext`).
- Flags **all active grants** for the employee as `revoke-flagged` and notifies IT.
- The **offboarding review lists any unrevoked (still `revoke-flagged`) access** so completion is tracked.

## UI

- **Kind**: background — event listener, no screen. Flagged grants surface in the Flagged tab of [[access-grants]]; unrevoked ones appear in the offboarding review.
- **Page**: none.
- **Trigger**: `EmployeeOffboarded` → `DeprovisionOnOffboardListener` → all active grants set to `revoke-flagged` + IT notification.

## Data

- Owns / writes: `it_access_grants` only (flags active grants `revoke-flagged`).
- Reads: employee reference carried on the event.
- Cross-domain writes: none — reacts to an HR event and writes only IT tables, never hr.profiles' tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeOffboarded` from [[../../../hr/employee-profiles/_module|hr.employee-profiles]] → flags all active grants for revocation + notifies IT.
- Feeds: flagged grants into [[access-grants]] (Flagged tab) and the offboarding review of unrevoked access.
- Shared entity: employee owned by hr.profiles (read via event only).

## Unknowns

- Whether flagged grants auto-revoke on a deadline or stay manual until IT completes — `*(assumed: manual, tracked to completion)*`.

## Related

- [[../_module|Access Provisioning]] · [[access-grants]] · [[provisioning-on-hire]] · [[../../../../architecture/event-bus]]
