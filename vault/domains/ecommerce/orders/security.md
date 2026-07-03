---
domain: ecommerce
module: orders
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Orders — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.orders.view-any` | View orders |
| `ecommerce.orders.update` | Edit orders / notes |
| `ecommerce.orders.mark-paid` | Manual `pending → paid` when payments inactive (fires `CheckoutCompleted`, deducts stock, queues receipt + mail) |
| `ecommerce.orders.fulfil` | Mark shipped / fulfil |
| `ecommerce.orders.refund` | Process refunds |
| `ecommerce.orders.cancel` | Cancel orders |

Seeded in `PermissionSeeder`. See [[../../../../security/authn-authz]].

## Rate Limiting

| Action | Limiter | Why |
|---|---|---|
| `mark-paid` (manual) | `panel-action` | queues confirmation mail (comms) + receipt PDF (file generation) + deducts stock (inventory mutation) |
| `refund` | `panel-action` | calls Stripe (external API) + mutates money; restock mutates inventory |
| `fulfil` | `panel-action` | may send a shipment/tracking notification *(assumed)*; state mutation |

`panel-action` per [[../../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. The public checkout `place` endpoint runs on the storefront's guest guard and is rate-limited there (see [[../../storefront/_module|storefront]] security) — its limiter name is reconciled with the public-endpoint registry as a known open task *(assumed)*.

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.orders.view-any')
        && BillingService::hasModule('ecommerce.orders');
}
```

Checkout (public Vue + Inertia) runs on the guest guard; the server re-validates the cart against live stock/prices before `OrderService::place` — the client cart is never trusted.

## Tenant Isolation

All three tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains queries. `CheckoutCompleted` carries `company_id` as a scalar so the finance listener runs under the correct company context. See [[../../../../security/tenancy-isolation]].

## Cross-Domain Write Boundary

Orders **never writes finance tables**. The sale is recorded by Finance's own listener reacting to `CheckoutCompleted`. Stock is only ever changed via `ProductStock`/`StockService`. See [[../../../../security/data-ownership]].

## Module Gating

`BillingService::hasModule('ecommerce.orders')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Customer email/name and shipping address are stored plaintext *(assumed — no encryption requirement documented)*. See [[unknowns]].
