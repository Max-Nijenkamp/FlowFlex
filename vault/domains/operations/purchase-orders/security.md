---
domain: operations
module: purchase-orders
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Purchase Orders — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.purchase-orders.view-any` | List POs |
| `operations.purchase-orders.create` | Create a PO (incl. create-from-requisition) |
| `operations.purchase-orders.send` | Send (draft→sent, PDF + mail) |
| `operations.purchase-orders.cancel` | Cancel a draft/sent PO |

Seeded in `PermissionSeeder`.

**Verb-per-transition check:** `send` covers draft→sent; `cancel` covers draft/sent→cancelled. The `sent → partially_received → received` transitions are driven by GRN's `recordReceipt` (same-domain) and are gated by `operations.goods-receipt.create`, not a separate PO verb.

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

| Action | Limiter | Why |
|---|---|---|
| `send` action (`GeneratePoPdfJob` + `PurchaseOrderMail`) | `panel-action` | Sends comms (supplier email) and generates a file (PO PDF) per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]; per-company throttle prevents PDF/email abuse ([[../../../build/security-audit-2026-06-11]], medium) |

Limiter registry: [[../../../architecture/security]].

## Data Ownership

Writes only `ops_purchase_orders`, `ops_po_lines`. `quantity_received`/status updates come from GRN via `recordReceipt` (same-domain service call). No stock or finance writes here ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
