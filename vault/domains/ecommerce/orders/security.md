---
domain: ecommerce
module: orders
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.orders.view-any` | View orders |
| `ecommerce.orders.update` | Edit orders / notes |
| `ecommerce.orders.fulfil` | Mark shipped / fulfil |
| `ecommerce.orders.refund` | Process refunds |
| `ecommerce.orders.cancel` | Cancel orders |

See [[../../../../security/authn-authz]].

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
