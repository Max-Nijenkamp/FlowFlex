---
domain: crm
module: pipeline
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Pipeline — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `crm.pipeline.view` | View the pipeline board |
| `crm.pipeline.move-deals` | Drag-and-drop deal stage changes |
| `crm.pipeline.manage-stages` | Create, reorder, delete pipeline stages |

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('crm.pipeline.view-any')
           && BillingService::hasModule('crm.pipeline')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages must state `canAccess()` explicitly.

Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope`
- `MoveDealData` validates both `deal_id` and `stage_id` belong to the current company before delegating to `DealService`
- Reverb channel `company.{id}.crm` is a private channel — requires authenticated user in the same company

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
