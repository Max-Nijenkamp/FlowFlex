---
domain: ecommerce
module: abandoned-cart
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Abandoned Cart

Detect carts abandoned before checkout and run recovery email sequences. Internal scheduled detection — the v1 `CartAbandoned` cross-domain event was dropped; recovery is same-domain *(assumed)*.

## Module-key

| Field | Value |
|---|---|
| key | `ecommerce.abandoned-cart` |
| priority | p3 |
| panel | ecommerce |
| permission-prefix | `ecommerce.abandoned-cart` |
| tables | `ec_carts`, `ec_cart_recovery_emails` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../storefront/_module\|Storefront]] | cart capture at checkout start |
| Hard | [[../orders/_module\|Orders]] | conversion detection stops the sequence |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] · [[../../foundation/queue-workers/_module\|Queues]] · [[../../foundation/email-setup/_module\|Email]] | gating, permissions, recovery mails |
| Soft | [[../promotions/_module\|Promotions]] | discount coupon in the 3rd mail |

## Core Features

- **Cart tracking** — contents + customer email captured when checkout starts but isn't completed.
- **Abandonment detection** — inactive past a configurable window (default 1h).
- **Recovery sequence** — 1st (1h), 2nd (24h), 3rd with discount (72h); steps configurable.
- **Recovery link** — signed token restores the cart for one-click resume.
- **Recovery tracking** — carts recovered, revenue recovered.
- **Stop on conversion** — order matched by email/cart token stops the sequence.
- **Discount incentive** — auto single-use coupon in the final mail (promotions active).
- **GDPR** — carts purged after 90 days *(assumed)*.

## See features/

- [[features/recover-cart|Recover Cart]] — detection + email sequence + signed restore link.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's abandoned cart data
- [ ] Module gating: artifacts hidden when `ecommerce.abandoned-cart` inactive
- [ ] Inactivity flips active → abandoned at the window.
- [ ] Steps fire once each at 1h/24h/72h; stop on conversion.
- [ ] Recovery link restores cart; order via link marks recovered.
- [ ] Discount coupon generated single-use (promotions active).
- [ ] Carts purged at 90d.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | orders by email/token | ecommerce.orders | Conversion detection; never writes `ec_orders` |
| Commands | single-use coupon create | ecommerce.promotions | 3rd-mail incentive (soft); via `DiscountEngine`/coupon service |
| Reads | cart snapshot at checkout start | ecommerce.storefront | Capture source |

**Data ownership:** `ecommerce.abandoned-cart` writes only `ec_carts` + `ec_cart_recovery_emails`. Conversion is detected by reading orders; the incentive coupon is created through promotions' service — it never writes order or coupon tables directly ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../orders/_module|Orders]] · [[../promotions/_module|Promotions]] · [[../../../../architecture/data-lifecycle]]
- [[../../../glossary]]
