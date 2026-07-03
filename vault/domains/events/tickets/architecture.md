---
domain: events
module: tickets
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Tickets — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `TicketService::purchase()` | service | Atomic `quantity_sold` increment; apply discount (brick/money); create Stripe PaymentIntent (idempotency key). Webhook success → purchase `paid` + `RegistrationService::confirm` + queue `GenerateTicketPdfJob` → `TicketMail`. |
| `TicketService::refund()` | service | Stripe refund (idempotency key) + `RegistrationService::cancel` + decrement `quantity_sold`. |
| `GenerateTicketPdfJob` | queued job | `spatie/laravel-pdf` ticket with the registration QR. |

## Purchase Flow

```mermaid
sequenceDiagram
    participant A as Attendee (Vue)
    participant T as TicketService
    participant S as Stripe
    participant R as RegistrationService
    A->>T: purchase(registration, ticket, discount?)
    T->>T: atomic quantity_sold++ (guard sold-out)
    T->>S: PaymentIntent (idempotency key)
    S-->>T: webhook payment_intent.succeeded (signature verified)
    T->>R: confirm(registration)  (same-domain call)
    T->>T: queue GenerateTicketPdfJob → TicketMail
```

## Money

- All amounts are integer minor units (`price_cents`, `amount_cents`); arithmetic via `brick/money`. Currency per ticket (`currency(3)`).

## Webhook Verification

- The inbound Stripe webhook **verifies the `Stripe-Signature` header (signing secret)** before processing payment-confirmation events (per [[../../../_archive/build-history/security-audit-2026-06-11]], HIGH). Webhook handled by shared Stripe routing *(assumed: per-domain event types)*.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `purchase` sold-count increment | Pessimistic | Atomic `quantity_sold` increment under `lockForUpdate` -- oversell-proof (inventory-capacity tier); Stripe idempotency key dedupes intents |
| Webhook success handling | Pessimistic | Purchase row locked -- paid transition + confirm + PDF queue fire once per webhook retry |
| `refund` | Pessimistic | Money mutation: Stripe refund (idempotency key) + sold-count decrement + cancel in one locked transaction |
| Ticket-type / discount CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| Ticket types relation manager | on `EventResource` | relation manager | Windows, quantities. |
| `TicketSalesWidget` | Events | #6 widget | Revenue + sold per event. |
| Purchases list | Events | #1 (read-only) | Refund action. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.tickets.view-any')
        && BillingService::hasModule('events.tickets');
}
```

Purchase flow is public (Vue + Stripe Elements, ui-strategy row #16).
