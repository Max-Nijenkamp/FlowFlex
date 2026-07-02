---
domain: ecommerce
module: orders
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders

Order management: line items, customer, payment + fulfilment status, and the order lifecycle. Fires `CheckoutCompleted` — the E-commerce → Finance bridge.

## Module-key

| Field | Value |
|---|---|
| key | `ecommerce.orders` |
| priority | p3 |
| panel | ecommerce |
| permission-prefix | `ecommerce.orders` |
| tables | `ec_orders`, `ec_order_lines`, `ec_order_events` |
| fires-events | `CheckoutCompleted` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../products/_module\|Products]] | lines reference products/variants |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] · [[../../foundation/queue-workers/_module\|Queues]] | gating, permissions, receipt jobs |
| Soft | [[../payments/_module\|Payments]] | paid status via Stripe; manual mark-paid otherwise |
| Soft | crm.contacts · operations.inventory · [[../promotions/_module\|Promotions]] · finance.tax | customer link, stock deduct, discounts, tax |

## Core Features

- **Order record** — number, customer, lines, totals, payment + fulfilment status.
- **State machine** — `pending → paid → fulfilled → completed | cancelled | refunded`.
- **Line items** — product/variant, qty, unit price, line total; **prices snapshot at order time**.
- **Totals** — subtotal, discount, tax, shipping, grand total (`brick/money`).
- **Customer** — linked to CRM contact (find-or-create, soft).
- **Fulfilment** — mark shipped with tracking; partial fulfilment; digital auto-fulfils.
- **Refunds** — full/partial (via payments when active).
- **Timeline** — append-only `ec_order_events`.
- **Receipt** — PDF (`spatie/laravel-pdf`) + confirmation mail.
- **Stock** — reserved at placement, deducted on paid, released on cancel (via `ProductStock`).

## See features/

- [[features/place-order|Place Order]] — checkout → priced/stocked order, fires `CheckoutCompleted`.
- [[features/fulfil-order|Fulfil Order]] — the fulfilment board (mark shipped, partial, tracking).

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Totals math exact (lines + discount + tax + shipping) via `brick/money`.
- [ ] Price snapshot immune to later product price changes.
- [ ] Stock reserve on place, deduct on paid, release on cancel.
- [ ] `markPaid` fires `CheckoutCompleted` with contract payload.
- [ ] Variant required when product has variants; over-stock order rejected.
- [ ] Partial fulfilment → partial status; digital auto-fulfils.
- [ ] Refund with restock returns stock.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `CheckoutCompleted` | finance (record sale), analytics (P3) | Payload: `company_id`, `order_id`, `customer_email`, `total_cents`, `currency` — finance's own listener writes finance tables |
| Reads/Commands | `ProductStock` (products) → `StockService` | operations.inventory | Reserve/deduct/release; never writes `ops_*` |
| Reads/Commands | `DiscountEngine::apply` | ecommerce.promotions | Discount lines at checkout |
| Reads/Commands | `ContactService::findOrCreateByEmail` | crm.contacts | Customer link (soft) |
| Reads | tax classes | finance.tax-management | Order-time tax calc |

**Data ownership:** `ecommerce.orders` writes only `ec_orders` + `ec_order_lines` + `ec_order_events`. It **never writes finance tables** — the sale reaches Finance via the `CheckoutCompleted` event, whose finance listener writes finance's own tables ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../payments/_module|Payments]] · [[../products/_module|Products]] · [[../../finance/invoicing/_module|Invoicing]]
- [[../../../../architecture/event-bus]] · [[../../../glossary]]
