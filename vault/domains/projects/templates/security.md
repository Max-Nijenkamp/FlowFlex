---
domain: projects
module: templates
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Templates — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.templates.view-any` | View templates |
| `projects.templates.create` | Create templates + save-as-template + duplicate-to-edit a system template |
| `projects.templates.update` | Edit company templates |
| `projects.templates.delete` | Soft-delete a company template |
| `projects.templates.instantiate` | Create a project from a template (the instantiate command verb) |

Seeded in `PermissionSeeder`. **Instantiate**, **save-as-template**, and **duplicate-to-edit** each map to a verb above (`instantiate` / `create`). Instantiation creates records only (no comms, money, files, or external calls), so it needs no rate limiter; the target modules' own create rights are enforced inside their owning actions.

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.templates.view-any')
        && BillingService::hasModule('projects.templates');
}
```

## System-Template Scope Exception (tenancy)

- System templates (`is_system = true`, `company_id` null) are surfaced to **all** tenants via a **read-only** global-scope exception.
- Any write/edit to a system template is **blocked cross-tenant**; editing copies it into the acting company first (copy-on-edit).
- Never allows a tenant to mutate the shared/system row. Reference [[../../../architecture/multi-tenancy]].

## Tenant Isolation

Company templates carry `company_id` via `BelongsToCompany`; `CompanyScope` applies except for the system read-only exception above. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.templates')`.

## Encrypted Fields

None.
