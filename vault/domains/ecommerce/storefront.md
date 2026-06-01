---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.storefront
status: planned
color: "#4ADE80"
---

# Storefront Configuration

Configure the public-facing storefront: theme, branding, navigation, pages, and checkout settings. The storefront itself is rendered via Vue + Inertia.

## Core Features

- Storefront settings: store name, logo, colours, currency, languages
- Navigation menu builder (categories, custom pages)
- Custom pages (About, Shipping, Returns) via rich text
- Checkout settings: required fields, guest checkout toggle, terms acceptance
- Shipping options: flat rate, free over threshold, per-region
- Tax settings: inclusive/exclusive, per-region rates
- Domain: storefront on subdomain or custom domain
- SEO defaults

## Data Model

| Table | Key Columns |
|---|---|
| `ec_storefront_settings` | company_id, store_name, theme_config (json), navigation (json), checkout_config (json), shipping_options (json) |
| `ec_storefront_pages` | company_id, title, slug, body, is_published |

Stored partly via `spatie/laravel-settings`.

## Filament

**Nav group:** Settings

- `StorefrontSettingsPage` (custom page) — tabbed config: branding, navigation, checkout, shipping, tax
- `StorefrontPageResource` — custom content pages

## Public Frontend

- Storefront rendered in Vue + Inertia (product browse, cart, checkout) — see [[frontend/_index]]

## Related

- [[domains/ecommerce/products]]
- [[domains/ecommerce/orders]]
- [[frontend/_index]]
