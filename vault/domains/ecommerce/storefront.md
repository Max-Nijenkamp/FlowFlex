---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.storefront
status: planned
priority: p3
depends-on: [ecommerce.products, ecommerce.orders, core.billing, core.rbac, core.settings]
soft-depends: [ecommerce.payments, ecommerce.promotions, ecommerce.reviews, finance.tax]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [ec_storefront_pages]
permission-prefix: ecommerce.storefront
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Storefront Configuration

Configure the public-facing storefront: theme, branding, navigation, pages, and checkout settings — AND owns the public Vue + Inertia storefront rendering (browse, cart, checkout).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/products\|ecommerce.products]] + [[domains/ecommerce/orders\|ecommerce.orders]] | what the storefront sells + checkout target |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/company-settings\|core.settings]] | gating, permissions, base branding |
| Soft | payments (Stripe checkout), promotions (cart discounts), reviews (display), finance.tax | checkout enrichments |

---

## Core Features

- Storefront settings: store name, logo, colours, currency, languages
- Navigation menu builder (categories, custom pages)
- Custom pages (About, Shipping, Returns) via rich text (purified)
- Checkout settings: required fields, guest checkout toggle, terms acceptance
- Shipping options: flat rate, free over threshold, per-region *(assumed: flat + threshold v1)*
- Tax settings: inclusive/exclusive display
- Domain: storefront at `/shop/{company-slug}` v1; custom domain = later ADR *(assumed)*
- SEO defaults
- **Public storefront (Vue + Inertia)**: product browse/search, product page (variants, reviews), cart (session), checkout (Stripe when active)

---

## Data Model

Settings via `spatie/laravel-settings` (`StorefrontSettings` class — theme_config, navigation, checkout_config, shipping_options).

### ec_storefront_pages — id, company_id (indexed), title, slug (unique per company), body (purified), is_published, deleted_at

---

## DTOs

### Cart/session structures (storefront): CartData — lines[{product_id, variant_id?, qty}], coupon_code? — server-validated against stock/prices at every step; checkout posts `CreateOrderData` (orders module)

## Services & Actions

- `StorefrontService::catalog(filters)` — active products, company-scoped, cached
- Cart: session-based *(assumed: DB carts only for abandoned-cart capture at checkout start)*
- Checkout flow: validate cart → `DiscountEngine` (soft) → `OrderService::place` → `EcPaymentService::createIntent` (soft) → confirmation

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `StorefrontSettingsPage` | #7 tabbed custom page | branding, navigation, checkout, shipping, tax |
| `StorefrontPageResource` | #1 CRUD resource | content pages |

Public storefront: Vue + Inertia — ui-strategy row #16 (browse, product, cart, checkout pages).


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('ecommerce.storefront.view-any') && BillingService::hasModule('ecommerce.storefront')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`ecommerce.storefront.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Storefront serves only active products of the right company
- [ ] Guest checkout toggle honored; required fields enforced
- [ ] Shipping: flat + free-over-threshold math
- [ ] Cart re-validated server-side (stale price/stock rejected)
- [ ] Custom pages published-only publicly; bodies purified
- [ ] Checkout end-to-end (Playwright): browse → cart → order placed

---

## Build Manifest

```
database/migrations/xxxx_create_ec_storefront_pages_table.php
app/Settings/StorefrontSettings.php
app/Models/Ecommerce/StorefrontPage.php
app/Services/Ecommerce/StorefrontService.php
app/Http/Controllers/Storefront/{CatalogController,ProductController,CartController,CheckoutController}.php
resources/js/Pages/Shop/{Index,Product,Cart,Checkout,Confirmation}.vue
app/Filament/Ecommerce/Pages/StorefrontSettingsPage.php
app/Filament/Ecommerce/Resources/StorefrontPageResource.php
tests/Feature/Ecommerce/{StorefrontTest,CheckoutFlowTest}.php
```

---

## Related

- [[domains/ecommerce/products]]
- [[domains/ecommerce/orders]]
- [[frontend/_index]]
