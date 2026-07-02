---
domain: it
module: helpdesk
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Helpdesk — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `it.helpdesk.create-own` | Create + view own tickets (all users — self-service) |
| `it.helpdesk.respond` | Reply to any ticket, add internal notes, resolve (IT staff) |
| `it.helpdesk.assign` | Assign / reassign tickets to IT team members |
| `it.helpdesk.view-any` | See all company tickets (IT staff) |

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('it.helpdesk.view-any')
           && BillingService::hasModule('it.helpdesk')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages (`ItHelpdeskQueuePage`) must state `canAccess()` explicitly.

Self-service ticket creation is available to all users via `it.helpdesk.create-own`; those users reach the create form / their own tickets without `view-any`.

Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope` ([[../../../security/tenancy-isolation]], [[../../../architecture/multi-tenancy]]).
- **Requester scope:** employees (holders of `it.helpdesk.create-own` only) see and reply to *their own* tickets — the resource query is filtered to `requester_employee_id = current employee`. IT staff (holders of `it.helpdesk.view-any` / `respond`) see all company tickets.
- `is_internal` replies are never returned to the requester's view and trigger no notification.
- Asset link (`asset_id`) for a requester is validated against that requester's own assigned assets before persist ([[data-model|helpdesk.data-model]]).
- `it.helpdesk` writes only `it_tickets` + `it_ticket_replies`; requester data is read from `hr.profiles` and the asset link is a read/FK to `it.assets` — never cross-domain writes ([[../../../security/data-ownership]]).
