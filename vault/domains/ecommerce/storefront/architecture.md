---
domain: ecommerce
module: storefront
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront — Architecture

## Rendering Model

Public storefront is **Vue + Inertia** (ui-strategy row #16) — the only public surface in E-commerce. Admin config is Filament. Thin Inertia controllers under `Http/Controllers/Storefront`.

## Services & Actions

| Method | Responsibility |
|---|---|
| `StorefrontService::catalog(filters)` | active, company-scoped products; cached |
| checkout flow | validate cart → `DiscountEngine` (soft) → `OrderService::place` → `EcPaymentService::createIntent` (soft) → confirmation |

Cart is session-based *(assumed: DB carts only for abandoned-cart capture at checkout start — see [[../../abandoned-cart/_module|Abandoned Cart]])*. The server re-validates cart stock/prices at **every** step; the client cart is never trusted.

## Settings

`spatie/laravel-settings` `StorefrontSettings` (theme_config, navigation, checkout_config, shipping_options). Content pages in `ec_storefront_pages`.

## Events

None fired/consumed. Checkout drives `OrderService::place`, which fires `CheckoutCompleted`. See [[../../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `StorefrontSettingsPage` | Settings | custom-page (tabbed) | branding, navigation, checkout, shipping, tax |
| `StorefrontPageResource` | Settings | simple-resource | content pages |

## Public Controllers (Vue + Inertia)

`CatalogController`, `ProductController`, `CartController`, `CheckoutController` → `resources/js/Pages/Shop/{Index,Product,Cart,Checkout,Confirmation}.vue`.

### Access contract

Admin config gates `ecommerce.storefront.manage` + `hasModule`. Public pages run on the **guest guard**, scoped to the company by `{company-slug}`.

## Jobs & Scheduling

None here (abandoned-cart scheduling lives in its own module).

## Search & Realtime

Storefront search filters `status = active` + company via products' Meilisearch index.
