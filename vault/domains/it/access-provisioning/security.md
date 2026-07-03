---
domain: it
module: access-provisioning
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Access Provisioning — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `it.access.view-any` | View systems, grants, templates and the access-review matrix |
| `it.access.grant` | Grant / advance an access grant |
| `it.access.revoke` | Revoke an access grant |
| `it.access.manage-systems` | Create / edit / delete the system catalogue |
| `it.access.manage-templates` | Create / edit / delete role access templates |
| `it.access.export-review` | Export the access-review matrix snapshot *(assumed)* |

Verb-per-command: `grant` / `revoke` cover the grant status transitions; `export-review` gates the matrix
export command on `AccessReviewPage`. Seeded in `PermissionSeeder`.

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('it.access.view-any')
           && BillingService::hasModule('it.access')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages (`AccessReviewPage`) must state
`canAccess()` explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per
[[../../../architecture/ui-strategy]]).

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope`
- `GrantAccessData` validates that both `employee_id` and `system_id` belong to the current company before `AccessService::grant`
- Queued listeners (`ProvisionOnHireListener`, `DeprovisionOnOffboardListener`) run under `WithCompanyContext` so the worker restores the tenant scope from the event's scalar `company_id` — avoids the null-team 403 family ([[../../../architecture/patterns/tenant-context-pitfalls]])
- Listeners write only IT tables; HR data is never mutated ([[../../../security/data-ownership]])

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].

---

## Rate Limiting

The matrix export action on `AccessReviewPage` generates a file and is throttled by the named **`exports`**
rate limiter (keyed on `company_id:user_id`) to prevent a full access-audit dump from being pulled in a
loop. Flagged medium in [[../../../_archive/build-history/security-audit-2026-06-11]]. See [[../../../architecture/security]].
