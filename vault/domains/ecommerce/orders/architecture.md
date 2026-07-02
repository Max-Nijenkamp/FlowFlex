---
domain: ecommerce
module: orders
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders — Architecture

## State Machine

`spatie/laravel-model-states` on `ec_orders.status`.

```
pending ──paid──> paid ──fulfilled──> fulfilled ──auto 14d──> completed
   │                 │                     │
   │cancel           │refund               │refund
   ▼                 ▼                     ▼
cancelled         refunded             refunded
```

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `pending` | `paid` | payment success (webhook) or manual mark-paid | fires `CheckoutCompleted`; stock deducted; receipt mailed |
| `pending` | `cancelled` | customer/admin | stock released |
| `paid` | `fulfilled` | all lines shipped / digital auto | tracking recorded |
| `paid`/`fulfilled` | `refunded` | refund processed | stock restock optional *(assumed: flag per refund)* |
| `fulfilled` | `completed` | auto 14d after fulfil *(assumed)* | — |

`fulfilment_status` (`unfulfilled/partial/fulfilled`) is tracked independently for partial shipments.

## Services & Actions

Interface → Service: `OrderServiceInterface` → `OrderService`.

| Method | Responsibility |
|---|---|
| `place(CreateOrderData): OrderData` | price snapshot, promotion/tax application (soft), stock reserve, order-number assignment |
| `markPaid(...)` | `pending → paid`; fires `CheckoutCompleted`; deducts stock; queues receipt PDF + confirmation mail |
| `fulfil(FulfilData)` | mark lines shipped, record tracking, set fulfilment status |
| `cancel(...)` | `pending → cancelled`; releases reserved stock |
| `refund(amount_cents, restock)` | full/partial refund; optional restock |

`AutoCompleteOrdersCommand` — scheduled; `fulfilled → completed` 14d after fulfilment *(assumed)*.

## Events

### Fires: `CheckoutCompleted`

| Field | Type |
|---|---|
| `company_id` | string |
| `order_id` | string |
| `customer_email` | string |
| `total_cents` | int |
| `currency` | string |

Consumers per [[../../../../architecture/event-bus]]: Finance (record sale — writes its own tables), Analytics (P3). Payload carries scalars/IDs only, never models.

## Filament Artifacts

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `EcOrderResource` | Orders | simple-resource | status filters; fulfil/refund/cancel actions; timeline relation |
| `OrderFulfilmentPage` | Orders | custom-page (board) | unfulfilled queue, mark-shipped workflow |
| `OrderStatsWidget` | Orders | widget | orders today, revenue, AOV |

Checkout itself is Vue + Inertia, owned by [[../../storefront/_module|storefront]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.orders.view-any')
        && BillingService::hasModule('ecommerce.orders');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Trigger |
|---|---|---|
| `GenerateReceiptPdfJob` | default | on `markPaid` |
| `OrderConfirmationMail` (ShouldQueue) | notifications | on `markPaid` |
| `AutoCompleteOrdersCommand` | default | scheduled daily |

## Search & Realtime

None required for v1 (admin list + board suffice).
