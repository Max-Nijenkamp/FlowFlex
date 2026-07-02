---
domain: customer-success
module: playbooks
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Playbooks — Security

## Permissions

| Permission | Description |
|---|---|
| `cs.playbooks.view-any` | View playbooks and runs |
| `cs.playbooks.manage` | Create / edit / delete playbook definitions |
| `cs.playbooks.run` | Launch a playbook run for an account |
| `cs.playbooks.complete-steps` | Mark run steps done / skipped |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('cs.playbooks.view-any')
           && BillingService::hasModule('cs.playbooks')
```

Per [[../../../architecture/filament-patterns]] #1. Definition edits require `cs.playbooks.manage`; launching requires `cs.playbooks.run`; step completion requires `cs.playbooks.complete-steps`. The one-click recovery launch from `cs.churn` still executes under `run` semantics and this module's own gate.

---

## Tenant Isolation

- All four tables carry `company_id` with a global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- The unique-active-run constraint is scoped by `company_id`.
- `PlaybookTriggerCommand` runs under `WithCompanyContext`, one company at a time.
- Trigger-signal reads (health, contracts renewal, account owner) go through each owning domain's tenant-scoped read API.

---

## Rate Limiting

Not applicable. No public/portal endpoints; mutating surfaces are gated panel actions.

---

## Encrypted Fields

None. Playbook definitions, steps, and run state are operational workflow data.
