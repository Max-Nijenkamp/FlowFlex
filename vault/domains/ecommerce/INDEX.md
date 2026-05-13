---
type: domain-index
domain: E-commerce
panel: ecommerce
panel-path: /ecommerce
panel-color: Teal
color: "#4ADE80"
---

# E-commerce

One panel for product catalogue, storefront, order management, payments, returns, promotions, abandoned carts, reviews, recommendations, multi-channel selling, bundles, subscriptions, and gift cards — replacing Shopify, WooCommerce, or BigCommerce.

**Panel:** `ecommerce` — `/ecommerce`
**Filament color:** Teal

---

## Modules

| Module | Key | Description |
|---|---|---|
| [[products]] | ecommerce.products | Product catalogue: variants, SKUs, pricing, images, and descriptions |
| [[storefront]] | ecommerce.storefront | Online storefront configuration: branding, layouts, domain, and published state |
| [[orders]] | ecommerce.orders | Order management: receive, fulfil, ship, track, and close with status workflow |
| [[payments]] | ecommerce.payments | Payment processing via Stripe: methods, refunds, and disputes |
| [[returns]] | ecommerce.returns | Return requests, RMA numbers, refund processing, and restocking |
| [[inventory-sync]] | ecommerce.inventory | Sync ecommerce stock with operations inventory in real time |
| [[promotions]] | ecommerce.promotions | Discount codes, percentage/fixed discounts, buy-X-get-Y, and time-limited offers |
| [[abandoned-carts]] | ecommerce.abandoned-carts | Abandoned cart tracking, recovery email sequences, and conversion rate |
| [[product-reviews]] | ecommerce.reviews | Customer product reviews: moderation, display, and rating aggregation |
| [[recommendations]] | ecommerce.recommendations | AI-powered product recommendations: related items and frequently bought together |
| [[multi-channel]] | ecommerce.multi-channel | Sell across storefront, Amazon, eBay, and social commerce channels |
| [[analytics]] | ecommerce.analytics | Revenue, conversion, AOV, customer LTV, and channel performance (read-only) |
| [[bundles]] | ecommerce.bundles | Fixed and mix-and-match bundle creation with bundle pricing |
| [[subscriptions]] | ecommerce.subscriptions | Recurring product subscriptions with billing cycles, pause/resume, and dunning |
| [[gift-cards]] | ecommerce.gift-cards | Digital gift card issuance, redemption tracking, and balance management |

---

## Nav Groups

- **Catalog** — products, bundles, storefront, inventory-sync
- **Orders** — orders, payments, returns, abandoned-carts
- **Marketing** — promotions, recommendations, product-reviews, gift-cards, subscriptions
- **Analytics** — analytics, multi-channel
- **Settings** — shipping zones, tax rules, payment methods

---

## Displaces

| Tool | Replaced By |
|---|---|
| Shopify | All 15 modules combined |
| WooCommerce | products, orders, payments, promotions |
| BigCommerce | products, storefront, orders, multi-channel |
| ReCharge | subscriptions |
| Yotpo | product-reviews, recommendations |

---

## Related

- [[../operations/inventory]] — inventory-sync pulls stock from operations
- [[../operations/logistics]] — order fulfilment triggers shipment creation
- [[../marketing/INDEX]] — abandoned cart emails and promotions use marketing email
- [[../finance/INDEX]] — order revenue and payment reconciliation
- [[../crm/INDEX]] — customer order history linked to CRM contacts
