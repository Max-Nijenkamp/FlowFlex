---
tags: [flowflex, filament, panels, access-control]
domain: Platform
status: built
last_updated: 2026-05-07
---

# Panel Map

All Filament panels in FlowFlex, their URLs, auth guards, and access rules.

## Panel Overview

| Panel ID | Domain | URL path | Access | Status |
|---|---|---|---|---|
| `admin` | Platform super-admin | `/admin` | FlowFlex staff only | ✅ built |
| `workspace` | Workspace settings, billing, module management | `/app/settings` | Workspace admins | ✅ built |
| `hr` | HR & People | `/app/hr` | HR team, managers | ✅ built (Phase 2) |
| `projects` | Projects & Work | `/app/projects` | All employees | ✅ built (Phase 2) |
| `finance` | Finance & Accounting | `/app/finance` | Finance team | ✅ built (Phase 3) |
| `crm` | CRM & Sales | `/app/crm` | Sales, support | ✅ built (Phase 3) |
| `operations` | Operations & Field Service | `/app/ops` | Ops, field teams | planned (Phase 4) |
| `ecommerce` | E-commerce | `/app/store` | Ecommerce team | planned (Phase 4) |
| `marketing` | Marketing & Content | `/app/marketing` | Marketing team | planned (Phase 5) |
| `communications` | Communications | `/app/comms` | All employees | planned (Phase 5) |
| `analytics` | Analytics & BI | `/app/analytics` | Managers, leadership | planned (Phase 6) |
| `it` | IT & Security | `/app/it` | IT team | planned (Phase 6) |
| `legal` | Legal & Compliance | `/app/legal` | Legal, compliance | planned (Phase 7) |
| `lms` | Learning & Development | `/app/learn` | All employees | planned (Phase 7) |

## Panel Access Rules

A panel is only visible to a Tenant if **both** conditions are true:

1. The module is active for their company (`Company::hasModuleForPanel()` → checks `company_modules` pivot)
2. The tenant is enabled (`is_enabled = true`) and their company is enabled

`Tenant::canAccessPanel()` enforces both conditions. Panel-level permission checks (`hr.panel.access`) are deferred to individual resource `canViewAny()` methods.

Super-admins (`User` model, `web` guard, `/admin` panel) are entirely separate from tenants.

## As-Built Panel Patterns

```php
// HrPanelProvider / ProjectsPanelProvider
->authGuard('tenant')
->authMiddleware([
    AuthenticateTenant::class,   // sets tenant session
    SetLocaleFromCompany::class, // applies company locale
])
->viteTheme('resources/css/filament/theme.css')

// Tenant::canAccessPanel()
if ($panel->getId() === 'workspace') return true;
return Cache::remember(
    "company:{$this->company_id}:panel:{$panel->getId()}:access",
    now()->addMinutes(5),
    fn () => $company->hasModuleForPanel($panel->getId())
);
```

All workspace panels use the `tenant` guard. The `admin` panel uses the default `web` guard.

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
