---
tags: [flowflex, domain/ecommerce, storefront, checkout, phase/4]
domain: Ecommerce
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Storefront & Checkout

Customisable branded storefront with a modern checkout experience. Each workspace gets a hosted storefront at their own domain or subdomain.

**Who uses it:** Ecommerce team (configure), customers (shop)
**Filament Panel:** `ecommerce`
**Depends on:** [[Product Catalogue]], [[Inventory Management]], [[CRM — Contact & Company Management]]
**Phase:** 4
**Build complexity:** Very High — 4 resources, 3 pages, 5 tables

---

## Features

- **Multi-storefront support** — a company can run multiple storefronts (e.g. B2C and B2B, or multiple brands); each with own domain and theme
- **Custom domain + subdomain** — storefronts hosted at `yourshop.flowflex.app` or custom domain via DNS CNAME
- **Theme customisation** — primary colour, logo, font, header/footer layout; no code required
- **Page builder** — CMS-style pages (homepage, about, landing pages) with a JSON content block structure; publish/unpublish per page
- **Product listings and filters** — category navigation, search, sort by price/rating/new; stock status shown in real-time
- **Product pages** — description, images, variants (size/colour), stock status, pricing, related products
- **Cart and checkout** — add to cart, update quantities, apply coupon codes, proceed to multi-step checkout
- **Guest checkout** — no account required; email used to create CRM contact automatically on order
- **Saved addresses** — returning customers can select from saved delivery/billing addresses
- **Payment processing** — Stripe (cards, Apple Pay, Google Pay), PayPal; Klarna BNPL (deferred); payment intent managed server-side
- **Cart abandonment recovery** — `CartAbandoned` event fires when cart expires without checkout; triggers email sequence via [[Email Marketing]]
- **Discount and coupon codes** — percentage or fixed discount; minimum order value; single or multi-use; applied at checkout
- **Shipping method selection** — flat rate, free over threshold, or carrier-calculated; configured per storefront
- **`CheckoutCompleted` event** — on successful payment, fires event consumed by [[Order Management]] to create order record
- **SEO meta per page** — title and description per storefront page and product page

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `storefronts`
| Column | Type | Notes |
|---|---|---|
| `name` | string | internal name |
| `domain` | string nullable | custom domain or subdomain slug |
| `theme` | string default 'default' | theme identifier |
| `is_active` | boolean default true | |
| `primary_color` | string nullable | hex code |
| `logo_file_id` | ulid FK nullable | → files |
| `settings` | json nullable | font, header/footer config |
| `default_currency` | string(3) default 'GBP' | |
| `tax_inclusive_pricing` | boolean default true | |

### `storefront_pages`
| Column | Type | Notes |
|---|---|---|
| `storefront_id` | ulid FK | → storefronts |
| `title` | string | |
| `slug` | string | |
| `content` | json | block-based content structure |
| `is_published` | boolean default false | |
| `published_at` | timestamp nullable | |
| `seo_title` | string nullable | |
| `seo_description` | text nullable | |
| `is_homepage` | boolean default false | |
| `sort_order` | integer default 0 | |

### `carts`
| Column | Type | Notes |
|---|---|---|
| `storefront_id` | ulid FK | → storefronts |
| `session_id` | string nullable | browser session for guests |
| `crm_contact_id` | ulid FK nullable | → crm_contacts (logged-in customers) |
| `coupon_code` | string nullable | |
| `discount_amount` | decimal(10,2) default 0 | |
| `expires_at` | timestamp | |
| `abandoned_email_sent_at` | timestamp nullable | |

### `cart_items`
| Column | Type | Notes |
|---|---|---|
| `cart_id` | ulid FK | → carts |
| `ec_product_id` | ulid FK | → ec_products |
| `ec_product_variant_id` | ulid FK nullable | → ec_product_variants |
| `quantity` | integer | |
| `unit_price` | decimal(10,2) | price at time of add |

### `checkout_sessions`
| Column | Type | Notes |
|---|---|---|
| `cart_id` | ulid FK | → carts |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `shipping_address` | json | name, line1, line2, city, postcode, country |
| `billing_address` | json | |
| `shipping_method` | string nullable | |
| `shipping_cost` | decimal(10,2) default 0 | |
| `coupon_code` | string nullable | |
| `discount_amount` | decimal(10,2) default 0 | |
| `subtotal` | decimal(10,2) | |
| `tax_total` | decimal(10,2) | |
| `total` | decimal(10,2) | |
| `status` | enum | `pending`, `payment_intent_created`, `completed`, `failed`, `abandoned` |
| `stripe_payment_intent_id` | string nullable | |
| `completed_at` | timestamp nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `CheckoutCompleted` | `checkout_session_id`, `crm_contact_id` | [[Order Management]] (create order record), [[CRM — Contact & Company Management]] (upsert contact) |
| `CartAbandoned` | `cart_id`, `crm_contact_id` | [[Email Marketing]] (trigger abandonment sequence) |

---

## Events Consumed

None — Storefront is a top-of-funnel producer.

---

## Permissions

```
ecommerce.storefronts.view
ecommerce.storefronts.create
ecommerce.storefronts.edit
ecommerce.storefronts.delete
ecommerce.storefront-pages.view
ecommerce.storefront-pages.create
ecommerce.storefront-pages.edit
ecommerce.storefront-pages.delete
ecommerce.storefront-pages.publish
ecommerce.carts.view
ecommerce.checkout-sessions.view
```

---

## Related

- [[Ecommerce Overview]]
- [[Product Catalogue]]
- [[Order Management]]
- [[Inventory Management]]
- [[Email Marketing]]
- [[CRM — Contact & Company Management]]
