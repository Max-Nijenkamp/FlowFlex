---
type: moc
domain: E-commerce & Sales Channels
panel: ecommerce
cssclasses: domain-ecommerce
phase: 4
color: "#0891B2"
last_updated: 2026-05-08
---

# E-commerce & Sales Channels — Map of Content

Product catalogue, order management, storefront, marketplace sync, subscriptions, digital products, AI recommendations, returns, abandoned cart, and B2B portal.

**Panel:** `ecommerce`  
**Phase:** 4–5  
**Migration Range:** `600000–649999`  
**Colour:** Cyan `#0891B2` / Light: `#ECFEFF`  
**Icon:** `heroicon-o-shopping-bag`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Product Catalogue | 4 | planned | Products, variants, bundles, pricing tiers, SEO |
| Order Management | 4 | planned | Orders, fulfilment, shipping labels, tracking |
| Storefront & Checkout | 4 | planned | Vue+Inertia public store, Stripe Elements checkout |
| [[marketplace-integration\|Marketplace Integration]] | 5 | planned | Bol.com, Amazon, Zalando — inventory sync, order consolidation |
| [[subscription-products\|Subscription Products]] | 5 | planned | Subscribe-and-save, subscription boxes, self-service portal |
| Digital Products & Downloads | 5 | planned | File delivery, licence keys, streaming |
| AI Product Recommendations | 5 | planned | Collaborative filtering, pgvector similarity, A/B |
| Returns & Refunds Management | 5 | planned | Self-service portal, carrier labels, Stripe refunds |
| Abandoned Cart Recovery | 5 | planned | Email+SMS+push sequences, AI discount rules |
| B2B Commerce Portal | 5 | planned | Wholesale portal, account pricing, PO workflow |
| [[product-reviews-ratings\|Product Reviews & Ratings]] | 5 | planned | Star ratings, UGC photos, Q&A, Google rich snippets |
| [[gift-cards-vouchers\|Gift Cards & Vouchers]] | 5 | planned | Digital/physical gift cards, promo codes, store credit |
| [[promotions-discount-engine\|Promotions & Discount Engine]] | 5 | planned | Flash sales, BOGO, bundles, tiered discounts, scheduling |
| [[product-bundles\|Product Bundles]] | 5 | planned | Fixed bundles, mix-and-match, kits, build-your-own |
| [[headless-commerce-api\|Headless Commerce API]] | 5 | planned | REST/GraphQL storefront API, SDK, Next.js starter, webhooks |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `CheckoutCompleted` | Storefront | Finance (record sale), Inventory (deduct), CRM (create/update contact) |
| `CartAbandoned` | Storefront | Marketing (recovery sequence) |
| `OrderFulfilled` | Order Management | Notifications (customer email/SMS) |
| `OrderReturned` | Returns | Finance (refund), Inventory (restock) |
| `SubscriptionRenewed` | Subscriptions | Finance (record MRR), CRM (update subscription) |
| `SubscriptionCancelled` | Subscriptions | Finance (churn), CRM (save flow) |

---

## Permissions Prefix

`ecommerce.products.*` · `ecommerce.orders.*` · `ecommerce.storefront.*`  
`ecommerce.channels.*` · `ecommerce.subscriptions.*` · `ecommerce.returns.*`

---

## Public Frontend

The storefront and checkout are Vue+Inertia public pages — see [[public-pages#storefront]].  
The B2B portal has a separate authenticated portal.

---

## Competitors Displaced

Shopify · WooCommerce · Magento · Bol.com Seller · Recharge (subscriptions) · Returnly (returns)

---

## Related

- [[MOC_Domains]]
- [[entity-product]]
- [[MOC_Finance]] — orders → revenue recording
- [[MOC_Operations]] — orders → inventory deduction
- [[MOC_Marketing]] — abandoned cart → email sequences
- [[MOC_Frontend]] — storefront = public Vue+Inertia pages
