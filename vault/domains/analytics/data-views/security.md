---
domain: analytics
module: data-views
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cross-Domain Data Views — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `analytics.data-views.view-any` | View the data-views gallery + open a view |
| `analytics.data-views.export` | Export a view's data to Excel |

---

## Access Contract

```php
canAccess() = Auth::user()->can('analytics.data-views.view-any')
           && BillingService::hasModule('analytics.data-views')
```

Per [[../../../architecture/filament-patterns]] #1 — the custom page states `canAccess()` explicitly.

---

## The domain-defining controls

1. **Cross-company isolation is the critical test.** Every source read inside `DataView::run()` goes through the owning domain's read path, which runs under `CompanyContext` — a view can only ever aggregate the current company's rows. Each shipped view carries its own tenant-isolation test.
2. **Module gating per source.** A view is listed only when **all** its `requiredModules()` are active; deactivating any source hides the view (no error).
3. **No free-form query.** Views are shipped code, not user-authored SQL — there is no injection surface.
4. **Export rate-limited** (medium, per [[../../../build/security-audit-2026-06-11]]): the export action is throttled ([[../../../architecture/security]]); generated files are tenant-scoped.

---

## Tenant Isolation

- Owns no tables; all reads are CompanyScope-safe through the source domains.
- Sharing/persistence n/a — views are stateless, rendered per request for the current company.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
