---
domain: support
module: automations
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Automations — Security

## Permissions

| Permission | Description |
|---|---|
| `support.automations.view-any` | View automation rules + logs |
| `support.automations.manage` | Create/edit/reorder/activate rules |

Seeded in `PermissionSeeder`.

## Access Contract

```php
canAccess() = Auth::user()->can('support.automations.view-any')
           && BillingService::hasModule('support.automations')
```

Per [[../../../architecture/filament-patterns]] #1.

## Data Ownership & Loop Guard

- Actions that change tickets go through `TicketService` — Automations never writes `sup_tickets` directly ([[../../../security/data-ownership]]).
- **Loop guard**: rule-driven updates are tagged with a system-actor flag *(assumed)* so they don't re-enter `AutomationEngine::evaluate` — prevents infinite rule cascades.
- Registry-validated fields/operators/action types: a malformed or injected condition/action cannot execute arbitrary logic.

## Tenant Isolation

All tables carry `company_id` (global `CompanyScope`); rules only ever see and act on their own company's tickets — [[../../../architecture/multi-tenancy]].

## Encrypted Fields

None.
