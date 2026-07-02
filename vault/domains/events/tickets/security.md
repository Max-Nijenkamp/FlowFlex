---
domain: events
module: tickets
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tickets — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.tickets.view-any` | View ticket types + purchases *(implied by access contract)* |
| `events.tickets.manage` | Create/edit ticket types + discount codes |
| `events.tickets.refund` | Issue refunds |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.tickets.view-any')
        && BillingService::hasModule('events.tickets');
}
```

## Webhook Verification (HIGH)

- The inbound Stripe webhook verifies the `Stripe-Signature` header against the signing secret **before** processing any payment-confirmation event. Unverified payloads are rejected. See [[../../../architecture/security]] and [[../../../build/security-audit-2026-06-11]].

## Rate Limiting

- **Public purchase endpoint** and **discount-code validation** are throttled to prevent abuse and code enumeration (per [[../../../build/security-audit-2026-06-11]], medium).

## Payment Idempotency

- PaymentIntent creation and refunds use Stripe idempotency keys; `stripe_payment_intent_id` is unique so a replayed webhook cannot double-confirm.

## Tenant Isolation

- All three tables carry `company_id` (indexed); `CompanyScope` constrains queries. Purchases resolve the ticket/registration strictly within the acting company. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None on ticket tables. Attendee PII lives in [[../registrations/_module|events.registrations]] (encrypted there).
