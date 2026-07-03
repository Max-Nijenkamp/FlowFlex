---
domain: ecommerce
module: storefront
feature: configure-storefront
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Configure Storefront

Merchant configures branding, navigation, checkout/shipping/tax settings, and manages content pages.

## Behaviour

- Edit `StorefrontSettings`: theme (name, logo, colours, currency, languages), navigation menu (categories + pages), checkout config (required fields, guest toggle, terms), shipping options (flat + free-over threshold), tax display.
- Manage `ec_storefront_pages`: title, slug, purified body, published flag. Only published pages are public.

## UI

- **Kind**: custom-page (tabbed settings) + simple-resource (pages)
- **Page**: `StorefrontSettingsPage` (`/ecommerce/storefront/settings`) + `StorefrontPageResource` (`/ecommerce/storefront/pages`), nav group **Settings**.
- **Layout**: settings page = tabs (Branding · Navigation · Checkout · Shipping · Tax); pages = table + rich-text form.
- **Key interactions**: edit + save each tab (validated); build the nav menu (drag/reorder *(assumed)*); publish/unpublish content pages.
- **States**: empty (no custom pages → CTA) · loading (settings skeleton) · error (validation toast) · selected (tab active / page row editing).
- **Gating**: `ecommerce.storefront.manage`.

## Data

- Owns / writes: `ec_storefront_pages`, `StorefrontSettings` only.
- Reads: `ec_categories` (nav builder).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: category tree (products) for the nav builder.
- Feeds: settings + pages consumed by the public [[browse-and-cart]] / [[checkout]] surfaces.
- Shared entity: `ec_categories` (products).

## Test Checklist

### Unit
- [ ] Settings validation: shipping flat + free-over threshold amounts integer minor units; page slug unique

### Feature (Pest)
- [ ] Only published `ec_storefront_pages` served publicly; unpublished -> 404
- [ ] Tenant isolation + permission: settings save gated on the manage verb, own company only

### Livewire
- [ ] `StorefrontSettingsPage` tabbed form validates and persists `StorefrontSettings`; canAccess() explicit; `StorefrontPageResource` hidden without permission/module

## Unknowns

- Theme depth (tokens vs template overrides); custom-domain onboarding (see [[../unknowns]]).

## Related

- [[../_module|Storefront]] · [[browse-and-cart]] · [[checkout]]
