---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.orders
status: planned
priority: p3
depends-on: [ecommerce.products, core.billing, core.rbac, foundation.queues]
soft-depends: [ecommerce.payments, ecommerce.variants, crm.contacts, operations.inventory, ecommerce.promotions, finance.tax]
fires-events: [CheckoutCompleted]
consumes-events: []
patterns: [states, money, pdf, events]
tables: [ec_orders, ec_order_lines, ec_order_events]
permission-prefix: ecommerce.orders
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Orders

Order management: line items, customer, payment status, fulfilment status, and order lifecycle.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/products\|ecommerce.products]] | lines reference products/variants |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, receipt jobs |
| Soft | [[domains/ecommerce/payments\|ecommerce.payments]] | paid status via Stripe; manual mark-paid without it |
| Soft | crm.contacts (customer link), operations.inventory (stock deduct), promotions (discounts), finance.tax | enrichments |

---

## Core Features

- Order record: order number, customer, line items, totals, payment status, fulfilment status
- Order status machine: `pending → paid → fulfilled → completed | cancelled | refunded`
- Line items: product/variant, qty, unit price, line total — **prices snapshot at order time**
- Totals: subtotal, discount, tax, shipping, grand total (brick/money)
- Customer details: linked to CRM contact (find-or-create soft)
- Fulfilment: mark shipped with tracking number; partial fulfilment; digital items auto-fulfil
- Refunds: full or partial (via payments module when active)
- Order notes and timeline (`ec_order_events`)
- Receipt PDF (spatie/laravel-pdf) + confirmation mail
- Stock: reserved at order placement, deducted on paid, released on cancel (via `ProductStock`)

---

## Data Model

### ec_orders

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| order_number | string | unique per company |
| customer_contact_id | ulid nullable | CRM link |
| customer_email / customer_name | string | snapshot |
| status | string default `pending` | state machine |
| fulfilment_status | string default `unfulfilled` | unfulfilled/partial/fulfilled |
| subtotal_cents / discount_cents / tax_cents / shipping_cents / total_cents | bigint | |
| currency | string(3) | |
| coupon_code | string nullable | |
| shipping_address | jsonb nullable | |
| tracking_number | string nullable | |
| deleted_at | timestamp nullable | kept 7y per [[architecture/data-lifecycle]] |

**Indexes:** `(company_id, status)`, `(company_id, customer_email)`

### ec_order_lines — id, order_id FK, company_id, product_id FK, variant_id nullable FK, description snapshot, quantity (> 0), unit_price_cents, line_total_cents
### ec_order_events — id, order_id FK, company_id, type, notes nullable, occurred_at (append-only timeline)

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `pending` | `paid` | payment success (webhook) or manual mark-paid | fires `CheckoutCompleted`; stock deducted; receipt mailed |
| `pending` | `cancelled` | customer/admin | stock released |
| `paid` | `fulfilled` | all lines shipped / digital auto | tracking recorded |
| `paid`/`fulfilled` | `refunded` | refund processed | stock restock optional *(assumed: flag per refund)* |
| `fulfilled` | `completed` | auto 14d after fulfil *(assumed)* | |

---

## DTOs

### CreateOrderData (storefront checkout) — customer {email (required), name}, lines[{product_id, variant_id?, quantity > 0}] min:1 (variant required when product has variants; stock validated), coupon_code?, shipping_address (required unless all digital)
### FulfilData — order_id (paid), line_ids? (partial), tracking_number?

## Services & Actions

Interface→Service: `OrderServiceInterface` → `OrderService`.

- `place(CreateOrderData): OrderData` — price snapshot, promotion/tax application (soft), stock reserve, number assignment
- `markPaid(...)` — transition + `CheckoutCompleted` + deduct stock
- `fulfil(FulfilData)` / `cancel` / `refund(amount_cents, restock)`

## Events

### Fires: CheckoutCompleted
| Payload field | Type |
|---|---|
| company_id | string |
| order_id | string |
| customer_email | string |
| total_cents | int |
| currency | string |

Consumers per [[architecture/event-bus]]: Finance (record sale), Analytics (P3).

---

## Filament

**Nav group:** Orders

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EcOrderResource` | #1 CRUD resource | status filters; fulfil/refund/cancel actions; timeline relation |
| `OrderFulfilmentPage` | #3-style board custom page | unfulfilled queue |
| `OrderStatsWidget` | #6 widget | orders today, revenue, AOV |

Checkout: Vue + Inertia storefront (ui-strategy row #16; storefront module).


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('ecommerce.orders.view-any') && BillingService::hasModule('ecommerce.orders')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`ecommerce.orders.view-any` · `ecommerce.orders.update` · `ecommerce.orders.fulfil` · `ecommerce.orders.refund` · `ecommerce.orders.cancel`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Totals math: lines + discount + tax + shipping exact (brick/money)
- [ ] Price snapshot immune to later product price changes
- [ ] Stock reserve on place, deduct on paid, release on cancel
- [ ] markPaid fires `CheckoutCompleted` with contract payload
- [ ] Variant required when product has variants; over-stock order rejected
- [ ] Partial fulfilment → partial status; digital auto-fulfils
- [ ] Refund with restock returns stock

---

## Build Manifest

```
database/migrations/xxxx_create_ec_orders_table.php
database/migrations/xxxx_create_ec_order_lines_table.php
database/migrations/xxxx_create_ec_order_events_table.php
app/Models/Ecommerce/{EcOrder,OrderLine,OrderEvent}.php
app/States/Ecommerce/Order/{OrderState,Pending,Paid,Fulfilled,Completed,Cancelled,Refunded}.php
app/Data/Ecommerce/{CreateOrderData,FulfilData,OrderData}.php
app/Contracts/Ecommerce/OrderServiceInterface.php
app/Services/Ecommerce/OrderService.php
app/Events/Ecommerce/CheckoutCompleted.php
app/Jobs/Ecommerce/GenerateReceiptPdfJob.php
app/Mail/Ecommerce/OrderConfirmationMail.php
app/Console/Commands/Ecommerce/AutoCompleteOrdersCommand.php
app/Filament/Ecommerce/Resources/EcOrderResource.php
app/Filament/Ecommerce/Pages/OrderFulfilmentPage.php
app/Filament/Ecommerce/Widgets/OrderStatsWidget.php
database/factories/Ecommerce/{EcOrderFactory,OrderLineFactory}.php
tests/Feature/Ecommerce/{OrderLifecycleTest,OrderTotalsTest,OrderStockTest}.php
```

---

## Related

- [[domains/ecommerce/payments]]
- [[domains/ecommerce/products]]
- [[domains/finance/invoicing]]
- [[architecture/event-bus]]
