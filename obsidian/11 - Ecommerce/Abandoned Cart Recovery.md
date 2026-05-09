---
tags: [flowflex, domain/ecommerce, abandoned-cart, recovery, phase/5]
domain: Ecommerce
panel: ecommerce
color: "#0891B2"
status: planned
last_updated: 2026-05-08
---

# Abandoned Cart Recovery

Automatically follow up when visitors leave without buying. Multi-channel recovery: email, SMS, push, and retargeting pixels. AI personalises the message and offer. Recovers 5–15% of abandoned revenue with zero manual effort.

**Who uses it:** Ecommerce managers, marketing teams
**Filament Panel:** `ecommerce`
**Depends on:** Core, [[Storefront & Checkout]], [[Email Marketing]], [[SMS & WhatsApp Marketing]], [[Push Notifications]], [[AI Infrastructure]]
**Phase:** 5

---

## Features

### Cart Abandonment Detection

- Track add-to-cart events for identified visitors (logged-in or email-captured)
- Trigger abandonment after configurable idle time (default: 60 minutes of inactivity)
- Exclude: already purchased, already in recovery sequence, unsubscribed
- Partial capture: if visitor typed email in checkout but didn't complete → still captured

### Recovery Sequences

- Multi-step sequences per channel: 1h → 24h → 72h messages
- Channel assignment: email (highest reach) + optional SMS + optional push
- Configurable delays, max message count per sequence
- Stop on conversion: if purchase made, cancel all pending messages in sequence
- Branch logic: if opened email but didn't click → send SMS; if clicked but didn't buy → offer discount

### Message Personalisation

- Cart contents displayed: product images, names, prices, quantities
- Stock urgency: "Only 2 left!" if inventory < threshold
- Social proof: "47 people bought this today"
- AI-generated subject lines (A/B tested automatically)
- AI selects discount offer: test 0% / 5% / 10% off based on cart value and customer segment

### Discount Management

- Auto-generated unique coupon codes per recovery email (prevents sharing)
- Discount types: % off, fixed off, free shipping
- Expiry: code expires in 48h to create urgency
- Finance integration: recovery-attributed revenue tracked separately

### Session Capture Methods

- Cart saved to server when: visitor logs in, fills email field in guest checkout, or explicit "save cart" click
- Anonymous sessions: cart stored in `cart_sessions` with browser fingerprint + cookie
- Re-identification: if visitor returns on same device, cart is restored automatically

### Analytics

- Abandonment rate: add-to-cart → checkout started → purchase funnel
- Recovery rate per sequence step
- Revenue recovered per period
- Best-performing subject lines, channel comparison
- Discount utilisation: how often coupons were used vs ignored

---

## Database Tables (3)

### `ecommerce_cart_sessions`
| Column | Type | Notes |
|---|---|---|
| `session_key` | string unique | cookie ID |
| `customer_id` | ulid FK nullable | if identified |
| `email` | string nullable | captured from checkout |
| `items` | json | [{product_id, qty, price}] |
| `subtotal` | decimal | |
| `checkout_step` | string nullable | cart / email / payment |
| `abandoned_at` | timestamp nullable | |
| `recovered_at` | timestamp nullable | |
| `recovery_order_id` | ulid FK nullable | |

### `ecommerce_recovery_sequences`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `steps` | json | [{delay_hours, channel, template_id, discount_pct}] |
| `is_active` | boolean | |
| `stock_urgency_threshold` | integer default 5 | |
| `max_discount_pct` | decimal default 10 | |

### `ecommerce_recovery_sends`
| Column | Type | Notes |
|---|---|---|
| `cart_session_id` | ulid FK | |
| `sequence_id` | ulid FK | |
| `step_index` | integer | |
| `channel` | enum | `email`, `sms`, `push` |
| `coupon_code` | string nullable | |
| `sent_at` | timestamp | |
| `opened_at` | timestamp nullable | |
| `clicked_at` | timestamp nullable | |
| `converted_at` | timestamp nullable | |

---

## Permissions

```
ecommerce.cart-recovery.view
ecommerce.cart-recovery.configure
ecommerce.cart-recovery.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Klaviyo | Omnisend | WooCommerce Recover |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€30+/mo) | ❌ (€16+/mo) | partial (paid plugin) |
| Multi-channel (email + SMS + push) | ✅ | ✅ | ✅ | ❌ |
| AI subject line generation | ✅ | ✅ | ❌ | ❌ |
| AI discount amount optimisation | ✅ | ❌ | ❌ | ❌ |
| Unique coupon per email | ✅ | ✅ | ✅ | partial |
| Finance revenue attribution | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[Ecommerce Overview]]
- [[Storefront & Checkout]]
- [[Email Marketing]]
- [[SMS & WhatsApp Marketing]]
- [[Push Notifications]]
- [[AI Content Studio]]
