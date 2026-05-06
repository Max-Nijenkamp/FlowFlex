---
tags: [flowflex, domain/ecommerce, overview, phase/5]
domain: E-commerce & Sales Channels
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-06
---

# Ecommerce Overview

Product catalogue, order management, storefront, marketplace sync, subscriptions, and digital products.

**Filament Panel:** `ecommerce`
**Domain Colour:** Teal `#0D9488` / Light: `#CCFBF1`
**Domain Icon:** `shopping-bag` (Heroicons)
**Phase:** 5

## Modules in This Domain

| Module | Description |
|---|---|
| [[Product Catalogue]] | Centralised product DB, variants, pricing |
| [[Order Management]] | Multi-channel orders, fulfillment, returns |
| [[Storefront & Checkout]] | Branded storefront, cart, Stripe/PayPal |
| [[Marketplace Channel Sync]] | Amazon, eBay, Etsy listing sync |
| [[Subscription Products]] | Recurring billing, dunning, bundles |
| [[Digital Products & Downloads]] | File delivery, licence keys, download limits |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `OrderPlaced` | [[Order Management]] | [[Inventory Management]] (deduct stock), [[Invoicing]] (record revenue), CRM (update customer) |

## Related

- [[Product Catalogue]]
- [[Order Management]]
- [[Storefront & Checkout]]
- [[Inventory Management]]
- [[Panel Map]]
