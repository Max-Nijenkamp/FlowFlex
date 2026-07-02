---
domain: crm
module: customer-segments
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Customer Segments — Security

## Permissions

| Permission | Grants |
|---|---|
| crm.segments.view-any | List/view segments |
| crm.segments.create | Create segments |
| crm.segments.update | Edit segments and membership |
| crm.segments.delete | Delete segments |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.segments.view-any')
        && hasModule('crm.segments');
}
```

## Tenant Isolation

Both tables carry `company_id` and are scoped by `CompanyScope`. `SegmentService::contacts()` resolves against the tenant-scoped contact set, so a compiled dynamic query can never reach across companies even if a stored condition references another tenant's IDs.

## Module Gating

Gated on `crm.segments` via `hasModule()` in `canAccess()`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.

## Notes

- Condition compilation validates every field/operator against an allowed registry (incl. custom-field keys) before building SQL — invalid keys are rejected at save, preventing arbitrary column injection. See [[../../../security/authn-authz]].
- Custom-field keys are drawn from the schemaless-attribute registry per [[../../../architecture/patterns/custom-fields]].
