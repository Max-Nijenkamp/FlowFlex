---
domain: ecommerce
module: orders
feature: place-order
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Place Order

Turn a validated cart into a priced, stock-reserved order; on payment, fire `CheckoutCompleted` to Finance.

## Behaviour

1. `OrderService::place(CreateOrderData)` — re-validates each line against live stock/prices (client cart untrusted).
2. Snapshots unit prices + descriptions; applies discounts (`DiscountEngine`, soft) and tax (finance.tax, soft).
3. Reserves stock via `ProductStock`; assigns `order_number`; totals via `brick/money`.
4. Order starts `pending`. On payment success (Stripe webhook) or manual mark-paid → `markPaid`: transition to `paid`, deduct stock, queue receipt PDF + confirmation mail, and **fire `CheckoutCompleted`**.
5. Cancel before paid releases reserved stock.

## UI

- **Kind**: public-vue (checkout) — the merchant-facing order record is `EcOrderResource` (simple-resource).
- **Page**: checkout at `/shop/{company-slug}/checkout` (Vue + Inertia, owned by [[../../storefront/_module|storefront]]); resulting order viewed at `EcOrderResource` (`/ecommerce/orders`).
- **Layout**: checkout = address + payment step; server re-validates cart at each step. Admin order view = header (status, totals), lines table, timeline, customer panel.
- **Key interactions**: submit checkout → `place` → payment intent → on success `markPaid`; admin "Mark paid" action when payments inactive.
- **States**: empty (empty cart blocks checkout) · loading (placing/paying spinner) · error (stale price/stock → re-validate + message; payment failure → order stays pending, retry link) · selected (order row → detail).
- **Gating**: checkout is public/guest; admin order actions gated `ecommerce.orders.view-any` / `.update`.

## Data

- Owns / writes: `ec_orders`, `ec_order_lines`, `ec_order_events` only.
- Reads / Commands: `ec_products`/`ec_variants` (price/stock snapshot), `ProductStock`→`StockService` (reserve/deduct), `DiscountEngine` (promotions), tax classes (finance.tax), `ContactService::findOrCreateByEmail` (crm.contacts).
- Cross-domain writes: NONE — the sale reaches Finance only via the fired `CheckoutCompleted` event; Finance's listener writes finance tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `CheckoutCompleted` → consumed by finance (record sale), analytics (P3).
- Shared entity: `crm_contacts`, `ec_products`, tax classes, ops stock — all owned elsewhere, reached via their services.

## Unknowns

- Whether `CheckoutCompleted` needs line-level revenue detail (see [[../unknowns]]).

## Related

- [[../_module|Orders]] · [[fulfil-order]] · [[../../payments/_module|Payments]] · [[../../../../architecture/event-bus]]
