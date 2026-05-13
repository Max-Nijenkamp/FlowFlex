---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.storefront
status: planned
color: "#4ADE80"
---

# Storefront

> Configure the online store's branding, layout, navigation, domain, and published state without touching code.

**Panel:** `ecommerce`
**Module key:** `ecommerce.storefront`

## What It Does

Storefront manages the presentation layer of the online store. Teams configure the theme (colours, typography, logo), define the navigation structure (menus, featured collections), set up the custom domain, and control which pages are published. The storefront renders server-side from the product, promotions, and content data managed in other modules. Configuration changes take effect on publish without a code deployment. Multiple storefronts can be configured for different brands or regions from a single account.

## Features

### Core
- Theme configuration: primary colour, secondary colour, font family, logo, favicon, and header/footer layout
- Navigation: main menu with nested dropdowns; footer links; mobile hamburger menu
- Homepage sections: hero banner (with CTA), featured collection grid, promotional banner, testimonials strip, newsletter signup
- Custom domain: connect own domain via CNAME or A record; SSL auto-provisioned
- Pages: create and publish static pages (About, Contact, FAQ, Shipping Policy, Returns Policy)
- Published state: draft (only visible to admin) → published (live to customers)

### Advanced
- Multi-storefront: configure separate storefronts for different brands or markets under one company account; each with its own domain and theme
- Announcement bar: scrolling top-of-page bar for promotions, shipping deadlines, or alerts
- Geo-redirects: detect visitor country and redirect to the appropriate regional storefront or show a currency/language selector
- Password protection: put the storefront behind a password for pre-launch or VIP-only access
- Cookie consent banner: configurable GDPR cookie consent with accept/reject; integrates with analytics tracking pixel settings
- Sitemap and robots.txt: auto-generated sitemap submitted to Google; robots.txt configurable

### AI-Powered
- Theme colour suggestion: given a logo image, suggest complementary theme colours
- Conversion optimisation tips: analyse current homepage layout and suggest changes based on ecommerce UX best practices

## Data Model

```erDiagram
    ec_storefronts {
        ulid id PK
        ulid company_id FK
        string name
        string custom_domain
        string status
        json theme_config
        json nav_structure
        json homepage_sections
        boolean password_protected
        string password_hash
        timestamps timestamps
    }

    ec_storefront_pages {
        ulid id PK
        ulid storefront_id FK
        string title
        string slug
        text content
        boolean published
        json seo_meta
        timestamps timestamps
    }

    ec_storefronts ||--o{ ec_storefront_pages : "contains"
```

| Table | Purpose |
|---|---|
| `ec_storefronts` | Storefront configuration, theme, and domain |
| `ec_storefront_pages` | Static pages with content and SEO meta |

## Permissions

```
ecommerce.storefront.view-any
ecommerce.storefront.configure
ecommerce.storefront.publish
ecommerce.storefront.manage-pages
ecommerce.storefront.manage-domain
```

## Filament

**Resource class:** `StorefrontResource`
**Pages:** List, Edit
**Custom pages:** `StorefrontThemeEditorPage` (visual theme configuration), `StorefrontPreviewPage` (live preview before publishing)
**Widgets:** `StorefrontStatusWidget` (published/draft status and domain SSL status)
**Nav group:** Catalog

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Themes | Storefront appearance configuration |
| BigCommerce Storefront | Theme and navigation management |
| WooCommerce Customiser | Theme configuration without code |
| Squarespace Commerce | No-code storefront setup |

## Implementation Notes

**Storefront rendering architecture:** The ecommerce storefront is NOT rendered by Filament — it is a public-facing Vue 3 + Inertia website per the tech-stack decision table ("Checkout and booking flows — Vue 3 + Inertia"). The `ecommerce` Filament panel manages configuration; the storefront itself is a separate Inertia SPA served from routes in `routes/web.php` under the storefront's custom domain.

**Custom domain routing:** `ec_storefronts.custom_domain` must be matched to an incoming request. The `StorefrontDomainMiddleware` (registered globally) reads the request's `Host` header, looks up `ec_storefronts` by `custom_domain`, and sets the `StorefrontContext` singleton for the request. All storefront routes then use this context to scope product, promotion, and page queries. SSL for the custom domain is handled at the infrastructure level (Cloudflare Proxy or an nginx reverse proxy with Let's Encrypt) — not by Laravel.

**`StorefrontThemeEditorPage`:** This is a custom Filament `Page` — a visual configuration form for colours, fonts, and layout options. It writes to `ec_storefronts.theme_config` (JSONB). The theme config is read by the Vue storefront's Inertia layout component and applied as CSS custom properties.

**`StorefrontPreviewPage`:** Renders the storefront's homepage in an `<iframe>` within the Filament panel. The iframe src is the storefront URL with a `?preview=1&panel_session={token}` parameter that bypasses the published state check (shows draft storefronts). A signed token authenticates the preview request.

**Geo-redirects:** Implemented as Inertia middleware on the storefront routes. Detect visitor country via IP geolocation (`geoip2/geoip2` PHP package with MaxMind GeoLite2 database — add to `composer.json`). Match country to configured regional storefronts. If a match exists and the visitor hasn't been redirected before (cookie check), redirect to the regional storefront domain.

**Cookie consent:** Rendered as a Vue component in the storefront layout. Consent state stored in a cookie (`flowflex_consent`). When the user accepts, analytics tracking scripts (Google Analytics, Meta Pixel) are loaded dynamically. When rejected, they are not loaded. This is client-side only — no backend table needed for basic GDPR compliance.

**Sitemap:** `SitemapController` generates a dynamic XML sitemap from active product, collection, and static page URLs for the storefront. Registered as a public route `GET /sitemap.xml`. Caches for 24 hours in Redis.

**AI features:** Theme colour suggestion sends the logo image URL to OpenAI Vision API (`gpt-4o` with image input) and requests complementary hex colours. Conversion optimisation tips call `app/Services/AI/StorefrontOptimisationService.php` with the current `homepage_sections` JSON and return a list of UX suggestions as plain text.

## Related

- [[products]] — active products rendered on the storefront
- [[promotions]] — active promotions shown in storefront banners
- [[analytics]] — storefront traffic and conversion tracked
- [[multi-channel]] — storefront is one of the configured channels
