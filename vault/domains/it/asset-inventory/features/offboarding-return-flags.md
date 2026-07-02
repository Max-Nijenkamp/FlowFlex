---
domain: it
module: asset-inventory
feature: offboarding-return-flags
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Offboarding Return Flags

When an employee is offboarded, flag their assigned assets for return and notify IT — automatically, via the HR event.

## Behaviour

- Consumes `EmployeeOffboarded` (from hr.profiles) with this module's **own** listener `FlagAssetsForReturnListener`.
- Listener finds the offboarded employee's currently-assigned `it_assets` and sets `return_flagged_at` on them (this module's own tables only).
- Notifies IT (via core.notifications) that assets are pending return.
- Queued (`ShouldQueue`) + `WithCompanyContext` so the correct tenant is resolved on the worker ([[../../../../architecture/event-bus]], [[../../../../architecture/patterns/tenant-context-pitfalls]]).
- hr.profiles never writes `it_assets` — the flag is set by this module reacting to the event.

## UI

- **Kind**: background — no screen; queued listener triggered by `EmployeeOffboarded`.
- **Page**: none. Flagged assets surface in `AssetResource` (a "return flagged" filter / badge on the list — [[asset-record]]).
- **Layout**: n/a (background). Notification lands in IT's core.notifications inbox.
- **Key interactions**: n/a — fully automated.
- **States**: n/a (no UI). Failure handling via the queue's retry/backoff.
- **Gating**: none (system-triggered); the resulting flagged list is viewed under `it.assets.view-any`.

## Data

- Owns / writes: `it_assets` (`return_flagged_at`) only.
- Reads: `EmployeeOffboarded` payload (carries `company_id` + employee id as scalars — no model reference).
- Cross-domain writes: none — notification is emitted via core.notifications; no HR/finance tables touched ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeOffboarded` from hr.profiles → flag that employee's assigned assets + notify IT.
- Feeds: an IT notification via core.notifications (no domain event fired back).
- Shared entity: `hr_employees` (the offboarded employee, identified by id in the payload).

## Unknowns

- Whether a follow-up reminder is sent if flagged assets are not returned within N days — not specified. See [[../unknowns|asset-inventory.unknowns]].

## Related

- [[../_module|Asset Inventory]] · [[assignment-return]] · [[../../../../architecture/event-bus]] · [[../../../../architecture/patterns/tenant-context-pitfalls]] · [[../../hr/employee-profiles/_module|hr.profiles]]
