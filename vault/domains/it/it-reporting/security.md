---
domain: it
module: it-reporting
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Reporting — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `it.reporting.view` | View the IT reporting dashboard and its widgets |

Single permission. Export uses the same `view` permission behind a named throttle (below).

---

## Access Contract

Every Filament artifact (the dashboard page and each widget) gates on `canAccess()`:

```php
canAccess() = Auth::user()->can('it.reporting.view-any')
           && BillingService::hasModule('it.reporting')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages must state `canAccess()` explicitly. Combines RBAC ([[../../../security/authn-authz]]) with module billing. Public/portal surfaces would use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

Soft-dep widgets add a second gate on their own module: a widget only renders when both `it.reporting` and its source module (e.g. `it.licences`) are active.

---

## Tenant Isolation

- Every aggregation is scoped by `company_id` — the service reads each source table through its owning module's read API under `CompanyContext`, so no cross-company rows can leak into a metric.
- The cache key `company:{id}:it:metrics:{from}:{to}` embeds `company_id`, so one company's cached aggregates can never be served to another.
- Read-only by construction: it.reporting owns no tables and issues no writes, so there is no write path that could escape the company scope ([[../../../security/data-ownership]]).

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].

---

## Rate Limiting

Cite a **named throttle on the report export action, keyed per company-user** (medium-severity finding, per [[../../../architecture/security]] / [[../../../_archive/build-history/security-audit-2026-06-11]]). Export generates an aggregate report on demand; the throttle prevents a single user from hammering the aggregation/PDF path.
