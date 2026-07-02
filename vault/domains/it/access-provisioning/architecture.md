---
domain: it
module: access-provisioning
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Access Provisioning — Architecture

See also [[_module|access-provisioning._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/event-bus]], [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].

---

## Services & Actions

- `AccessService::grant(GrantAccessData $data): AccessGrant` — creates/advances a grant, stamps `granted_at` / `granted_by`, writes activity audit; rejects a duplicate active `(employee_id, system_id)`
- `AccessService::revoke(AccessGrant $grant): void` — stamps `revoked_at` / `revoked_by`, transitions to `revoked`, writes activity audit
- `ProvisionOnHireListener` — consumes `EmployeeHired`; matches an `it_access_templates` row by job role name → creates one pending grant per template system in `it_access_grants` + fires an IT notification. No matching template = no grants + no error.
- `DeprovisionOnOffboardListener` — consumes `EmployeeOffboarded`; flags every active grant for the employee as `revoke-flagged` and notifies IT. Actual revocation is completed manually (tracking model).
- `AccessReviewQuery::matrix(): array` — builds the employees × systems matrix over `it_access_grants`; one query, no N+1

Both listeners are **queued** (`ShouldQueue`) and run under **`WithCompanyContext`** so the tenant scope
is restored on the worker (per [[../../../architecture/event-bus]] and
[[../../../architecture/patterns/tenant-context-pitfalls]]). Listeners write only IT tables — they never
touch hr.profiles data ([[../../../security/data-ownership]]).

---

## Events

**Consumes** (from [[../../hr/employee-profiles/_module|hr.profiles]], contracts per [[../../../architecture/event-bus]]):

| Event | Listener | Effect |
|---|---|---|
| `EmployeeHired` | `ProvisionOnHireListener` | pending grants from matching role template + IT notification |
| `EmployeeOffboarded` | `DeprovisionOnOffboardListener` | flag all active grants `revoke-flagged`, notify IT |

Both events carry `company_id` as a scalar. This module **fires no cross-domain events** in v1.

---

## Filament Artifacts

**Nav group:** Access

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `SystemResource` | #1 CRUD resource | tool catalogue |
| `AccessGrantResource` | #1 CRUD resource | pending / flagged tabs, grant / revoke actions |
| `AccessTemplateResource` | #1 CRUD resource | role → systems templates |
| `AccessReviewPage` | #9 matrix custom page | employees × systems, throttled export |

Pattern reference: [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].
Every artifact gates on `canAccess()` — see [[security|access-provisioning.security]].

---

## Search & Realtime

None. The matrix is computed on demand from `it_access_grants`; no Meilisearch index and no Reverb
channel for this module in v1.

---

## Jobs & Scheduling

No scheduled jobs. The only async work is the two queued listeners above; all grant/revoke operations
are synchronous through `AccessService`.
