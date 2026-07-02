---
domain: operations
module: purchase-orders
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Purchase Orders — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.purchase-orders.view-any` | List POs |
| `operations.purchase-orders.create` | Create a PO |
| `operations.purchase-orders.send` | Send (draft→sent, PDF + mail) |
| `operations.purchase-orders.cancel` | Cancel a draft/sent PO |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.purchase-orders.view-any')
           && BillingService::hasModule('operations.purchase-orders')
```

Per [[../../../architecture/filament-patterns]] #1.

---

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope`.
- PO numbering sequence is per company; company A cannot see/send/cancel company B's POs.

## Rate Limiting

Per [[../../../build/security-audit-2026-06-11]] (medium): the `send` action and `GeneratePoPdfJob` + `PurchaseOrderMail` dispatch must be throttled per company to prevent PDF-generation / email abuse.

## Data Ownership

Writes only `ops_purchase_orders`, `ops_po_lines`. `quantity_received`/status updates come from GRN via `recordReceipt` (same-domain service call). No stock or finance writes here ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
