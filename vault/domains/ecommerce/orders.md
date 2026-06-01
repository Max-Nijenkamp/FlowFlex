---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.orders
status: planned
color: "#4ADE80"
---

# Orders

Order management: line items, customer, payment status, fulfilment status, and order lifecycle.

## Core Features

- Order record: order number, customer, line items, totals, payment status, fulfilment status
- Order status machine: `pending → paid → fulfilled → completed | cancelled | refunded`
- Line items: product/variant, qty, unit price, line total
- Totals: subtotal, discount, tax, shipping, grand total (brick/money)
- Customer details: linked to CRM contact
- Fulfilment: mark shipped with tracking number; partial fulfilment
- Refunds: full or partial
- Order notes and timeline
- Invoice/receipt PDF (spatie/laravel-pdf)

## Data Model

| Table | Key Columns |
|---|---|
| `ec_orders` | company_id, order_number, customer_contact_id, status, payment_status, fulfilment_status, subtotal_cents, tax_cents, shipping_cents, discount_cents, total_cents, currency |
| `ec_order_lines` | order_id, company_id, product_id, variant_id, quantity, unit_price_cents, line_total_cents |
| `ec_order_events` | order_id, company_id, type, notes, occurred_at |

## Filament

**Nav group:** Orders

- `OrderResource` — list (filter by status), view, fulfil action, refund action
- `OrderFulfilmentPage` (custom page) — fulfilment board
- `OrderStatsWidget` — orders today, revenue, avg order value

## Cross-Domain / Events

- Fires `CheckoutCompleted` → Finance (record sale), Operations (deduct stock), CRM (update contact)

## Related

- [[domains/ecommerce/payments]]
- [[domains/ecommerce/products]]
- [[domains/finance/invoicing]]
