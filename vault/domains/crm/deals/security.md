---
domain: crm
module: deals
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deals — Security

## Permissions

| Permission | Description |
|---|---|
| `crm.deals.view-any` | List all deals in the company |
| `crm.deals.view` | View a single deal |
| `crm.deals.create` | Create a new deal |
| `crm.deals.update` | Edit a deal |
| `crm.deals.delete` | Soft-delete a deal |
| `crm.deals.close` | Mark a deal won or lost |
| `crm.deals.reopen` | Reopen a won or lost deal *(assumed)* |

Seeded in `PermissionSeeder`.

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('crm.deals.view-any')
           && BillingService::hasModule('crm.deals')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages must state this explicitly.

---

## Tenant Isolation

- All tables carry `company_id` with a global `CompanyScope`.
- Company A cannot see, move, or close Company B's deals.
- Meilisearch search is tenant-scoped — see [[../../../architecture/search]].

---

## Module Gating

`DealResource` and all deal pages must be hidden when the `crm.deals` module is inactive in `BillingService`.

`CreateInvoiceAction` is additionally gated on `hasModule('finance.invoicing')` — it must be invisible when that module is inactive.

---

## Encrypted Fields

None planned for v1.
