---
domain: ecommerce
module: storefront
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront — Decisions

## ADR: Storefront owns the public Vue + Inertia rendering

- **Decision:** The single public E-commerce surface (browse/product/cart/checkout) is Vue + Inertia (ui-strategy row #16) and lives in the storefront module. Other modules expose read/command services; storefront renders.
- **Consequences:** One place owns public rendering + guest-guard security; catalogue/orders/etc. stay admin-Filament.

## ADR: Server re-validates cart at every step (client cart untrusted)

- **Decision:** Prices, stock, and discounts are recomputed server-side on cart view, checkout, and order placement.
- **Consequences:** No client-side price/stock tampering; stale carts get corrected before order.

## ADR: `/shop/{company-slug}` for v1, custom domains later

- **Decision:** Storefronts live at a slug path in v1; custom domains are a later ADR *(assumed)*.
- **Consequences:** No DNS/cert provisioning in v1; simpler routing.

## ADR: Session cart, DB capture only for abandoned-cart

- **Decision:** Live cart is session-based; a DB `ec_carts` row is captured at checkout start for recovery (owned by [[../../abandoned-cart/_module|Abandoned Cart]]) *(assumed)*.
- **Consequences:** Light cart state; persistence is the abandoned-cart module's concern.
