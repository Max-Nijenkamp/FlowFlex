---
domain: ecommerce
module: storefront
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront

Configure the public storefront (theme, navigation, pages, checkout settings) AND own the public Vue + Inertia rendering: browse, product, cart, checkout.

## Module-key

| Field | Value |
|---|---|
| key | `ecommerce.storefront` |
| priority | p3 |
| panel | ecommerce |
| permission-prefix | `ecommerce.storefront` |
| tables | `ec_storefront_pages` (+ `StorefrontSettings` via spatie/laravel-settings) |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../products/_module\|Products]] · [[../orders/_module\|Orders]] | what it sells + checkout target |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] · [[../../core/company-settings/_module\|Settings]] | gating, permissions, base branding |
| Soft | [[../payments/_module\|Payments]] · [[../promotions/_module\|Promotions]] · [[../reviews/_module\|Reviews]] · finance.tax | checkout enrichments |

## Core Features

- **Storefront settings** — store name, logo, colours, currency, languages.
- **Navigation builder** — categories + custom pages.
- **Custom pages** — About/Shipping/Returns (rich text, purified).
- **Checkout settings** — required fields, guest toggle, terms acceptance.
- **Shipping options** — flat rate, free over threshold *(assumed: flat + threshold v1)*.
- **Tax display** — inclusive/exclusive.
- **Domain** — `/shop/{company-slug}` v1; custom domain later *(assumed)*.
- **Public storefront (Vue + Inertia)** — browse/search, product page (variants, reviews), session cart, checkout (Stripe when active).

## See features/

- [[features/browse-and-cart|Browse & Cart]] — public catalog browse, product page, session cart.
- [[features/checkout|Checkout]] — validate cart → discount → place order → pay → confirm.
- [[features/configure-storefront|Configure Storefront]] — admin settings + content pages.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's storefront data
- [ ] Module gating: artifacts hidden when `ecommerce.storefront` inactive
- [ ] Storefront serves only active products of the right company.
- [ ] Guest checkout toggle honored; required fields enforced.
- [ ] Shipping: flat + free-over-threshold math.
- [ ] Cart re-validated server-side (stale price/stock rejected).
- [ ] Custom pages published-only publicly; bodies purified.
- [ ] Checkout end-to-end (Playwright): browse → cart → order placed.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | active products/categories/variants | ecommerce.products / variants | Catalog rendering |
| Commands | `OrderService::place` | ecommerce.orders | Checkout target; storefront never writes `ec_orders` |
| Commands | `EcPaymentService::createIntent` | ecommerce.payments | Stripe checkout (soft) |
| Commands | `DiscountEngine::apply` | ecommerce.promotions | Cart discounts (soft) |
| Reads | approved reviews + rating | ecommerce.reviews | Product page (soft) |

**Data ownership:** `ecommerce.storefront` writes only `ec_storefront_pages` + `StorefrontSettings`. Orders/payments/discounts happen through their owning services — storefront never writes their tables ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../products/_module|Products]] · [[../orders/_module|Orders]] · [[../../../../frontend/_index]]
- [[../../../glossary]]
