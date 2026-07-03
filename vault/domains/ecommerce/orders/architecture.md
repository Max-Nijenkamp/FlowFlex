---
domain: ecommerce
module: orders
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Orders

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `EcOrderResource` | #1 CRUD resource | tweaks: state-badge-column (order status), custom-header-actions (mark-paid / fulfil / refund / cancel), relation-manager-timeline (`ec_order_events`) | status + fulfilment filters; lines table; read-mostly (checkout owns creation) |
| `OrderFulfilmentPage` | #3 Kanban custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] — read-only queue (unfulfilled · partial), no free drag reorder; expand-to-ship | "Fulfilment" at `/ecommerce/orders/fulfilment`; polling 30s (single-operator queue) *(assumed — not collaborative, no Reverb)* |
| `OrderStatsWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | orders today, revenue, AOV; widget polling 30–60s |

**Public storefront (Vue + Inertia):**

- Checkout is Vue + Inertia ([[../../../architecture/ui-strategy]] row #16), owned by [[../../storefront/_module|storefront]] — it POSTs `CreateOrderData`; the server re-validates cart stock/prices before `OrderService::place`. Not a Filament artifact here.

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('ecommerce.orders.view-any') && BillingService::hasModule('ecommerce.orders')`
per [[../../../architecture/filament-patterns]] #1. `OrderFulfilmentPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages; its fulfil action additionally requires `ecommerce.orders.fulfil`. The public checkout surface runs on the guest guard in [[../../storefront/_module|storefront]], not here.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Order note / edit (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| `place` (checkout) — stock reservation | Pessimistic | `DB::transaction()` + `lockForUpdate()` on each product/variant stock row (via `ProductStock`/`StockService`) — oversell prevention; the whole order + line insert is one transaction |
| `markPaid` / `cancel` / `refund` / `fulfil` state transitions | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the order, re-read, validate, write per [[../../../architecture/patterns/states]]; `markPaid` deducts stock and `cancel`/`refund`(restock) release it inside the same lock |
| Totals mutation (`brick/money`) | Pessimistic | computed inside the order transaction above — never a bare read-modify-write on money columns |
| `ec_order_events` append | n/a | append-only timeline — no in-place update, no lock needed |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Trigger |
|---|---|---|
| `GenerateReceiptPdfJob` | default | on `markPaid` |
| `OrderConfirmationMail` (ShouldQueue) | notifications | on `markPaid` |
| `AutoCompleteOrdersCommand` | default | scheduled daily |

## Search & Realtime

None required for v1 (admin list + board suffice).
