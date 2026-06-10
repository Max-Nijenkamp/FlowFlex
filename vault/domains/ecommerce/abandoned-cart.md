---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.abandoned-cart
status: planned
priority: p3
depends-on: [ecommerce.storefront, ecommerce.orders, core.billing, core.rbac, foundation.queues, foundation.email]
soft-depends: [ecommerce.promotions]
fires-events: []
consumes-events: []
patterns: [queues, email]
tables: [ec_carts, ec_cart_recovery_emails]
permission-prefix: ecommerce.abandoned-cart
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Abandoned Cart

Detect carts abandoned before checkout and trigger recovery email sequences to recover lost sales. (Internal scheduled detection — the v1 spec's `CartAbandoned` event dropped: recovery is same-domain *(assumed)*.)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/storefront\|ecommerce.storefront]] | cart capture at checkout start |
| Hard | [[domains/ecommerce/orders\|ecommerce.orders]] | conversion detection stops sequence |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, recovery mails |
| Soft | [[domains/ecommerce/promotions\|ecommerce.promotions]] | discount coupon in 3rd mail |

---

## Core Features

- Cart tracking: contents + customer email captured when checkout started but not completed
- Abandonment detection: cart inactive for configurable time (default 1h)
- Recovery email sequence: 1st reminder (1h), 2nd (24h), 3rd with discount (72h) — steps configurable
- Recovery link: signed token restores the cart for one-click resume
- Recovery tracking: which carts were recovered, revenue recovered
- Stop sequence on completed purchase (order matched by email/cart token)
- Discount incentive in final mail (auto-generated single-use coupon when promotions active)
- Marketing suppression list honored ([[domains/marketing/campaigns]] convention) *(assumed: own opt-out link v1)*
- GDPR: carts purged after 90 days *(assumed)*

---

## Data Model

### ec_carts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| customer_contact_id | ulid nullable | |
| customer_email | string | |
| items | jsonb | snapshot |
| total_cents | bigint | |
| currency | string(3) | |
| status | string default `active` | active/abandoned/recovered/converted |
| last_activity_at | timestamp | |
| recovery_token | uuid unique | |
| order_id | ulid nullable | conversion link |

### ec_cart_recovery_emails — id, cart_id FK, company_id, step (1–3, unique per cart), sent_at, opened_at/clicked_at nullable

---

## DTOs

None public-input beyond storefront cart capture; recovery link restores session cart from snapshot.

## Services & Actions

- `CartRecoveryService::detect()` — active carts past inactivity window → abandoned
- `CartRecoveryService::advance()` — due steps per schedule, once each (unique step rows); stops on converted/recovered
- `RestoreCartController` — public token link → session cart + `recovered` status on subsequent order
- Conversion check: order with matching email/token → `converted`, sequence stops

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessAbandonedCartsCommand` | notifications | every 15 min | step unique rows + status guards |
| `PruneCartsCommand` | default | daily | 90d guard |

---

## Filament

**Nav group:** Marketing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `AbandonedCartResource` | #1 (read-only) | status, recovery funnel |
| `CartRecoveryWidget` | #6 widget | recovery rate, revenue recovered |

---

## Permissions

`ecommerce.abandoned-cart.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Inactivity flips active→abandoned at window
- [ ] Steps fire once each at 1h/24h/72h; stop on conversion
- [ ] Recovery link restores cart; order via link marks recovered
- [ ] Discount coupon generated single-use (promotions active)
- [ ] Carts purged at 90d

---

## Build Manifest

```
database/migrations/xxxx_create_ec_carts_table.php
database/migrations/xxxx_create_ec_cart_recovery_emails_table.php
app/Models/Ecommerce/{Cart,CartRecoveryEmail}.php
app/Services/Ecommerce/CartRecoveryService.php
app/Http/Controllers/Storefront/RestoreCartController.php
app/Mail/Ecommerce/CartRecoveryMail.php
app/Console/Commands/Ecommerce/{ProcessAbandonedCartsCommand,PruneCartsCommand}.php
app/Filament/Ecommerce/Resources/AbandonedCartResource.php
app/Filament/Ecommerce/Widgets/CartRecoveryWidget.php
database/factories/Ecommerce/CartFactory.php
tests/Feature/Ecommerce/CartRecoveryTest.php
```

---

## Related

- [[domains/ecommerce/orders]]
- [[domains/ecommerce/promotions]]
- [[architecture/data-lifecycle]]
