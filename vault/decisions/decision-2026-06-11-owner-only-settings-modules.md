---
type: adr
date: 2026-06-11
status: decided
domain: Core
color: "#F97316"
---

# Owner-only company settings + module marketplace

## Context

Company settings and module activation/deactivation change company-wide behaviour and the monthly bill. Permission grants (core.settings.update, core.marketplace.view) could be handed to any role by an admin, opening billing-relevant controls to non-owners.

## Options Considered

1. Permissions only (status quo) — flexible, but a role grant silently exposes billing controls
2. Owner role required IN ADDITION to the permission — belt and braces
3. Separate "billing admin" role — overkill at current size

## Decision

Option 2 (founder decision). `CompanySettingsPage` and `ModuleMarketplacePage` `canAccess()` now require `hasRole('owner')` AND the permission AND the module. Staff can appoint additional owners from the staff console (Make owner action on the company's Users tab).

## Consequences

- Non-owner users with the permission get 403 on both pages (regression-tested both ways)
- Owner appointment is a staff-console or future tenant-RBAC concern
- Pattern available for other billing-sensitive surfaces

## Related

- [[domains/core/staff-console/_module]]
- [[domains/core/company-settings/_module]], [[domains/core/module-marketplace/_module]]
