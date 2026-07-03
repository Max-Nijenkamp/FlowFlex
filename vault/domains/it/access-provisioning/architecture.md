---
domain: it
module: access-provisioning
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SystemResource` | #1 CRUD resource | tweaks: — | tool catalogue; list column `# active grants` |
| `AccessGrantResource` | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (grant / revoke) | Pending / Flagged status tabs; row actions call `AccessService::grant` / `::revoke` |
| `AccessTemplateResource` | #1 CRUD resource | tweaks: inline-relation-repeater (systems) | role → systems templates; jsonb `systems` repeater |
| `AccessReviewPage` | #18 heat-map / matrix custom page | [[../../../architecture/patterns/page-blueprints#Heat-map / Matrix]] | employees × systems grid; `exports`-throttled snapshot export *(assumed)* |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('it.access.view-any') && BillingService::hasModule('it.access')`
per [[../../../architecture/filament-patterns]] #1. `AccessReviewPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. This module exposes no public/portal surface.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| System / template CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Grant creation (duplicate-active guard) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the active `(employee_id, system_id)` to reject a concurrent second live grant per [[../../../architecture/patterns/states]] |
| Grant advance / revoke status stamp | Optimistic | `updated_at` stale-check on the grant record ([[../../../architecture/patterns/optimistic-locking]]) |
| Hire pending-grant / offboard flag (queued listeners) | n-a | event-driven single writer under `WithCompanyContext`; template match + `revoke-flagged` flag are idempotent, no concurrent user edit on the same path |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

None. The matrix is computed on demand from `it_access_grants`; no Meilisearch index and no Reverb
channel for this module in v1.

---

## Jobs & Scheduling

No scheduled jobs. The only async work is the two queued listeners above; all grant/revoke operations
are synchronous through `AccessService`.
