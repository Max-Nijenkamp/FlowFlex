---
domain: support
module: sla
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# SLA Management — Security

## Permissions

| Permission | Description |
|---|---|
| `support.sla.view` | View SLA policies, monitor, and compliance |
| `support.sla.manage` | Create/edit SLA policies and targets |

Seeded in `PermissionSeeder`.

## Access Contract

```php
canAccess() = Auth::user()->can('support.sla.view')
           && BillingService::hasModule('support.sla')
```

Per [[../../../architecture/filament-patterns]] #1 — `SlaMonitorPage` states this explicitly.

## Tenant Isolation

All tables carry `company_id` with global `CompanyScope`. SLA math reads `sup_tickets` timestamps + `core.settings` business hours strictly within the company scope — [[../../../architecture/multi-tenancy]].

## Encrypted Fields

None.
