---
tags: [flowflex, filament, panels, access-control]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Panel Map

All Filament panels in FlowFlex, their URLs, auth guards, and access rules.

## Panel Overview

| Panel ID | Domain | URL path | Access |
|---|---|---|---|
| `admin` | Platform super-admin | `/admin` | FlowFlex staff only |
| `workspace` | Workspace settings, billing, module management | `/app/settings` | Workspace admins |
| `hr` | HR & People | `/app/hr` | HR team, managers |
| `projects` | Projects & Work | `/app/projects` | All employees |
| `finance` | Finance & Accounting | `/app/finance` | Finance team |
| `crm` | CRM & Sales | `/app/crm` | Sales, support |
| `marketing` | Marketing & Content | `/app/marketing` | Marketing team |
| `operations` | Operations & Field Service | `/app/ops` | Ops, field teams |
| `it` | IT & Security | `/app/it` | IT team |
| `legal` | Legal & Compliance | `/app/legal` | Legal, compliance |
| `ecommerce` | E-commerce | `/app/store` | Ecommerce team |
| `lms` | Learning & Development | `/app/learn` | All employees |
| `communications` | Communications | `/app/comms` | All employees |
| `analytics` | Analytics & BI | `/app/analytics` | Managers, leadership |

## Panel Access Rules

A panel is only visible to a user if **both** conditions are true:

1. The module is active for their tenant (`tenant_modules` table)
2. The user has at least one permission within that panel (`hr.panel.access`, etc.)

Filament's `canAccess()` method on each panel checks both conditions.

Super-admins see all panels regardless.

## Implementation Pattern

```php
// In each Panel Provider
public function canAccess(): bool
{
    $user = auth()->user();
    $tenant = Filament::getTenant();

    // Check 1: module is active for this tenant
    if (!$tenant->hasModuleActive('hr')) {
        return false;
    }

    // Check 2: user has panel access permission
    return $user->can('hr.panel.access');
}
```

## Panel Domain Colours

See [[Filament Implementation]] for the colour mapping per panel.

## Panels with Module Dependencies

| Panel | Module that must be active |
|---|---|
| `hr` | HR module |
| `projects` | Projects module |
| `finance` | Finance module |
| `crm` | CRM module |
| `marketing` | Marketing module |
| `operations` | Operations module |
| `it` | IT module |
| `legal` | Legal module |
| `ecommerce` | E-commerce module |
| `lms` | Learning module |
| `communications` | Communications module |
| `analytics` | Analytics module |
| `workspace` | Always available to workspace admins |
| `admin` | FlowFlex staff only — never tenant-visible |

## Related

- [[Admin Panel]]
- [[Workspace Panel]]
- [[Filament Implementation]]
- [[Roles & Permissions (RBAC)]]
- [[Multi-Tenancy & Workspace]]
- [[Architecture]]
