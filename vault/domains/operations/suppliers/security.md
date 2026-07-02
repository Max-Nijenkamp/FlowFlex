---
domain: operations
module: suppliers
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Suppliers — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.suppliers.view-any` | List suppliers + catalogues + performance |
| `operations.suppliers.manage` | Create / edit / delete suppliers + supplied items |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.suppliers.view-any')
           && BillingService::hasModule('operations.suppliers')
```

Per [[../../../architecture/filament-patterns]] #1.

---

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope`.
- Performance queries over PO/GRN are tenant-scoped; company A cannot read company B's suppliers or their PO history.

## Data Ownership

Writes only `ops_suppliers`, `ops_supplier_items`. Reads PO/GRN rows for metrics (read-only). `fin_supplier_id` is a *reference* to finance.ap's supplier — never a write into finance tables ([[../../../security/data-ownership]]).

## Encrypted Fields

None here. Bank/IBAN details live in [[../../finance/accounts-payable/_module|finance.ap]] (encrypted there), not in operational supplier records.
