---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.abandoned-cart
status: planned
color: "#4ADE80"
---

# Abandoned Cart

Detect carts abandoned before checkout and trigger recovery email sequences to recover lost sales.

## Core Features

- Cart tracking: capture cart contents + customer email when checkout started but not completed
- Abandonment detection: cart inactive for configurable time (e.g. 1 hour)
- Recovery email sequence: 1st reminder (1h), 2nd (24h), 3rd with discount (72h)
- Recovery link: restores the cart for one-click resume
- Recovery tracking: which carts were recovered, revenue recovered
- Stop sequence on completed purchase
- Discount incentive in later emails (optional coupon)

## Data Model

| Table | Key Columns |
|---|---|
| `ec_carts` | company_id, customer_contact_id, customer_email, items (json), total_cents, status (active/abandoned/recovered/converted), last_activity_at, recovery_token |
| `ec_cart_recovery_emails` | cart_id, company_id, step, sent_at, opened_at, clicked_at |

## Filament

**Nav group:** Marketing

- `AbandonedCartResource` — list abandoned carts, recovery status
- `CartRecoveryWidget` — recovery rate, revenue recovered

## Cross-Domain / Events / Jobs

- Fires `CartAbandoned` → triggers recovery email sequence (queued)
- Uses [[architecture/email]] + [[architecture/queue-jobs]]

## Related

- [[domains/ecommerce/orders]]
- [[domains/marketing/email-sequences]]
