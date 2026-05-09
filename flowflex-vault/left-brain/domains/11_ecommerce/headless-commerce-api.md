---
type: module
domain: E-commerce & Sales Channels
panel: ecommerce
cssclasses: domain-ecommerce
phase: 5
status: planned
migration_range: 600000–649999
last_updated: 2026-05-09
---

# Headless Commerce API

Serve product catalogue, cart, checkout, and order management via pure REST/GraphQL API — decoupled from FlowFlex's built-in storefront. Customers build custom frontends (Next.js, React Native, kiosk, voice commerce) while FlowFlex manages the commerce backend.

**Panel:** `ecommerce` (management) + Storefront API (public, no auth)  
**Phase:** 5

---

## Why Headless

FlowFlex's default storefront (Phase 4) is pre-built, fast to launch, and covers 80% of needs. But:
- Enterprise customers have brand-bespoke frontends (custom Next.js sites)
- Mobile apps need native checkout experience
- B2B customers embed ordering into their own ERP portals
- Kiosk, POS, voice commerce, AR shopping are non-browser channels

Headless = FlowFlex as commerce backend; any frontend can consume it.

---

## API Endpoints

### Storefront API (public, no auth required for browsing)
```
GET  /api/storefront/v1/products
GET  /api/storefront/v1/products/{slug}
GET  /api/storefront/v1/categories
GET  /api/storefront/v1/search?q=...
POST /api/storefront/v1/cart               — create cart
GET  /api/storefront/v1/cart/{token}       — get cart
PUT  /api/storefront/v1/cart/{token}/items — add/update items
POST /api/storefront/v1/checkout           — begin checkout
POST /api/storefront/v1/checkout/{id}/payment — submit payment
GET  /api/storefront/v1/orders/{token}     — order confirmation
```

### Customer API (requires customer JWT)
```
POST /api/storefront/v1/auth/login
GET  /api/storefront/v1/account
GET  /api/storefront/v1/account/orders
GET  /api/storefront/v1/account/addresses
POST /api/storefront/v1/account/wishlist
```

### GraphQL (alternative to REST)
- Full product catalogue query with fragments
- Cart mutations
- Order queries
- Subscription via websocket for cart updates

### Webhooks (push events to custom frontend)
- `checkout.completed`
- `order.status_changed`
- `product.updated`
- `inventory.low_stock`

## Features

### SDK & Developer Experience
- TypeScript SDK (npm: `@flowflex/storefront`)
- Next.js starter template (App Router + RSC)
- React Native starter
- Postman collection
- API versioning (v1, v2 with deprecation notices)
- Sandbox environment with pre-seeded product data
- Rate limiting: 1000 req/min per API key

### Performance
- CDN-cached product responses (edge-cached, invalidated on product update)
- Cart sessions stored in Redis (fast, no DB hit per cart update)
- Webhook delivery with retry (3 attempts, exponential backoff)
- Real-time inventory availability (no caching — live DB check on add-to-cart)

### Multi-Region
- API available on regional endpoints (EU, US, APAC) for low latency
- GDPR: EU customer data stays in EU endpoint

---

## Permissions

```
ecommerce.headless.manage-api-keys
ecommerce.headless.view-api-usage
ecommerce.headless.configure-webhooks
```

---

## Related

- [[MOC_Ecommerce]]
- [[MOC_CorePlatform]] — API layer, auth tokens
- [[left-brain/architecture/auth-rbac.md]] — Sanctum tokens for customer API auth
