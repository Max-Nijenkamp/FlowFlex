---
domain: ecommerce
module: payments
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payments — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.payments.view-any` | View payments |
| `ecommerce.payments.refund` | Process refunds |

See [[../../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.payments.view-any')
        && BillingService::hasModule('ecommerce.payments');
}
```

## Webhook Security (baseline)

- **Stripe signature verification** on every webhook (reject → 400).
- **Rate limiter** on the webhook route (`throttle:webhooks`) — from [[../../../../build/security-audit-2026-06-11]] (medium).
- **Idempotency** — replaying the same `payment_intent` is a no-op (unique intent id + status guard).
- **No card data** stored locally; only Stripe references. Idempotency keys on all Stripe mutations.

See [[../../../../architecture/security]].

## Tenant Isolation

`ec_payments` carries `company_id` (indexed); the webhook resolves the company from the order/intent and runs under that company's context. See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.payments')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None locally — card data never touches FlowFlex storage (Stripe holds it).
