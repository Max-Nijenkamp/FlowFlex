---
domain: crm
module: activities
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Activities — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `crm.activities.view-any` | View the activity list and any timeline |
| `crm.activities.view` | View a single activity |
| `crm.activities.create` | Log a new activity |
| `crm.activities.update` | Edit an activity |
| `crm.activities.delete` | Soft-delete an activity |
| `crm.activities.complete` | Mark a task/meeting activity done (`CompleteTaskAction`) + optional follow-up |

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('crm.activities.view-any')
           && BillingService::hasModule('crm.activities')
```

Per [[../../../architecture/filament-patterns]] #1.

Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope`
- `LogActivityData` validates all foreign keys (contact_id, deal_id, account_id, owner_id) belong to the current company
- `TaskReminderCommand` queries filtered by `company_id` — no cross-tenant notification leakage

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
