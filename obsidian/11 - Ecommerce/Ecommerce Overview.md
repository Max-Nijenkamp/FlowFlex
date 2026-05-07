---
tags: [flowflex, domain/ecommerce, overview, phase/4]
domain: E-commerce & Sales Channels
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Ecommerce Overview

Product catalogue, order management, storefront, marketplace sync, subscriptions, and digital products. All 6 modules built in Phase 4 as a complete panel.

**Filament Panel:** `ecommerce`
**Domain Colour:** Teal `#0D9488` / Light: `#CCFBF1`
**Domain Icon:** `heroicon-o-shopping-bag`
**Phase:** 4 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[Product Catalogue]] | Products, variants, categories, brands, images, pricing rules, tax codes |
| [[Order Management]] | Orders, order lines, fulfillment, shipments, returns, refunds, channel refs |
| [[Storefront & Checkout]] | Storefronts, pages, carts, checkout sessions, Stripe/PayPal |
| [[Marketplace Channel Sync]] | Amazon/eBay/Etsy/Shopify connections, listings, sync logs |
| [[Subscription Products]] | Plans, subscriptions, invoices, dunning, trial management |
| [[Digital Products & Downloads]] | Digital products, download links, licence keys, streaming |

## Filament Panel Structure

**Navigation Groups:**
- `Products` — Products, Variants, Categories, Brands, Pricing Rules
- `Orders` — Orders, Fulfillments, Returns, Refunds
- `Storefront` — Storefronts, Storefront Pages
- `Channels` — Marketplace Channels, Channel Listings
- `Subscriptions` — Subscription Plans, Subscriptions
- `Digital` — Digital Products, Download Links, Licence Keys

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `OrderPlaced` | Order Management | Inventory (deduct stock), Invoice (record revenue), CRM (update customer) |
| `CheckoutCompleted` | Storefront | Order Management (create order), Email (post-purchase sequence) |
| `CartAbandoned` | Storefront | Email (abandoned cart sequence) |
| `OrderShipped` | Order Management | Notifications (notify customer) |
| `ReturnRequested` | Order Management | Notifications (notify ops team) |
| `ChannelSyncCompleted` | Marketplace Sync | Notifications (summary) |
| `ChannelSyncFailed` | Marketplace Sync | Notifications (alert team) |
| `SubscriptionStarted` | Subscriptions | Email (welcome), Finance (create invoice) |
| `SubscriptionRenewed` | Subscriptions | Finance (create renewal invoice) |
| `SubscriptionCancelled` | Subscriptions | CRM (update contact), Email (win-back sequence) |
| `PaymentFailed` | Subscriptions | Dunning flow, customer notification |
| `DownloadLinkGenerated` | Digital Products | Email (delivery to customer) |

## Permissions Prefix

`ecommerce.products.*` · `ecommerce.orders.*` · `ecommerce.storefront.*`  
`ecommerce.channels.*` · `ecommerce.subscriptions.*` · `ecommerce.digital.*`

## Database Migration Range

`500000–599999`

## Note on Product Tables

Ecommerce product tables use prefix `ec_` (e.g. `ec_products`, `ec_product_variants`) to avoid collision with Operations `products` table.

## Related

- [[Product Catalogue]]
- [[Order Management]]
- [[Storefront & Checkout]]
- [[Marketplace Channel Sync]]
- [[Subscription Products]]
- [[Digital Products & Downloads]]
- [[Inventory Management]] (Operations — stock deducted on order)
- [[Invoicing]] (Finance — revenue recorded on sale)
- [[Panel Map]]
- [[Build Order (Phases)]]
