---
domain: it
module: software-licences
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Software Licences — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `it.licences.view-any` | View licences, seats, utilisation, renewals |
| `it.licences.manage` | Create, edit, delete licence records |
| `it.licences.assign` | Assign and revoke seats per employee |

Verb-per-command: `assign` covers both the seat-assign and seat-revoke actions (capacity mutations);
`manage` covers licence CRUD. Renewal alert and offboard reclaim are system-triggered (scheduled command /
queued listener), no user command. Seeded in `PermissionSeeder`.

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('it.licences.view-any')
           && BillingService::hasModule('it.licences')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages and widgets must state `canAccess()` explicitly.

Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope` on both `it_licences` and `it_licence_assignments`.
- `AssignSeatData` validates `licence_id` and `employee_id` belong to the current company before `AssignSeatAction` writes.
- `FlagSeatsForReclaimListener` runs under `WithCompanyContext` keyed to the `EmployeeOffboarded` event's `company_id` scalar — it only touches assignments in that company (see [[../../../architecture/patterns/tenant-context-pitfalls]]).
- Cross-domain writes never occur: HR data is read-only to this module; the reclaim flag writes only this module's `it_licence_assignments` ([[../../../security/data-ownership]]).

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].

---

## Rate Limiting

Seat assign / revoke mutate licence capacity, so those panel actions are throttled by the named
**`panel-action`** limiter *(assumed)* to prevent a rapid assign/revoke loop. See [[../../../architecture/security]].
