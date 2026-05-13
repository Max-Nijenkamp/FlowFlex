---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.multi-channel
status: planned
color: "#4ADE80"
---

# Multi-Channel

> Sell across multiple channels — own storefront, Amazon, eBay, and social commerce — with centralised product listings, inventory, and order management.

**Panel:** `ecommerce`
**Module key:** `ecommerce.multi-channel`

## What It Does

Multi-Channel extends the ecommerce domain beyond the own storefront to external marketplaces and social commerce platforms. Product listings are pushed from the FlowFlex product catalogue to connected channels; inventory is synchronised in real time from the same pool (preventing overselling); and orders from all channels flow back into the central order management system for unified fulfilment. Merchants manage one product catalogue and fulfil from one warehouse rather than logging into each marketplace separately.

## Features

### Core
- Channel connections: Amazon Seller Central, eBay, Meta Shops (Facebook/Instagram), Google Shopping, TikTok Shop
- Product listing push: map FlowFlex product catalogue fields to channel-specific required fields; push to create or update listings
- Inventory sync: channel stock levels updated from the same inventory pool as the own storefront via [[inventory-sync]]
- Order pull: orders placed on connected channels pulled into FlowFlex as standard orders in [[orders]]
- Channel-specific pricing: set a different price per channel (e.g., higher price on Amazon to cover fees)
- Listing status per channel: active, inactive, error — see the status of every product on every channel from one screen

### Advanced
- Attribute mapping: map FlowFlex product attributes to Amazon browse node requirements, eBay item specifics, and Google product types
- Channel fee modelling: input fee rates per channel; see estimated margin per channel for each product
- Repricing rules: configure automatic price adjustments per channel based on competitor pricing or a target margin
- Multi-currency pricing: set channel prices in the local currency of the marketplace (USD for Amazon US, EUR for Amazon DE)
- Channel-specific fulfilment: configure FBA (Fulfilled by Amazon) for Amazon orders while own storefront orders are self-fulfilled
- Listing bulk sync: push catalogue changes (price update, new images, stock status) to all channels in one action

### AI-Powered
- Listing optimisation: suggest improved product titles, bullet points, and descriptions formatted for each marketplace's search algorithm
- Channel mix recommendation: based on category and margin, recommend which channels to activate for a given product

## Data Model

```erDiagram
    ec_channel_connections {
        ulid id PK
        ulid company_id FK
        string channel
        json api_credentials
        boolean is_active
        timestamp last_synced_at
        timestamps timestamps
    }

    ec_channel_listings {
        ulid id PK
        ulid product_variant_id FK
        ulid channel_connection_id FK
        string external_listing_id
        decimal channel_price
        string currency
        string status
        json channel_attributes
        timestamps timestamps
    }

    ec_channel_connections ||--o{ ec_channel_listings : "contains"
```

| Table | Purpose |
|---|---|
| `ec_channel_connections` | Connected marketplace accounts |
| `ec_channel_listings` | Per-variant listing status per channel |

## Permissions

```
ecommerce.multi-channel.view-any
ecommerce.multi-channel.manage-connections
ecommerce.multi-channel.manage-listings
ecommerce.multi-channel.sync
ecommerce.multi-channel.delete
```

## Filament

**Resource class:** `ChannelConnectionResource`, `ChannelListingResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ChannelListingBulkSyncPage` (push updates to all connected channels)
**Widgets:** `ChannelOrderVolumeWidget` (orders per channel this week), `ListingErrorsWidget`
**Nav group:** Analytics

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Linnworks | Multi-channel order and listing management |
| ChannelAdvisor | Marketplace listing and order management |
| Sellbrite | Multi-channel listing and inventory sync |
| Skubana / Extensiv | Omnichannel order management |

## Implementation Notes

**External dependency — marketplace APIs (each requires separate integration work):**
- **Amazon Seller Central:** Use the Amazon Selling Partner API (SP-API). OAuth2 LWA (Login with Amazon) auth flow. Listing management via `Listings Items API`. Order retrieval via `Orders API`. Inventory update via `Fulfillment Inventory API`. Requires registering as an Amazon developer and submitting for SP-API approval — this process takes 1–4 weeks. Store Amazon `refresh_token`, `seller_id`, `marketplace_id` in `ec_channel_connections.api_credentials` (encrypted JSONB).
- **eBay:** eBay REST APIs. OAuth2. Listing via `Inventory API`. Orders via `Fulfillment API`. Requires eBay developer account and production app approval.
- **Meta Shops (Facebook/Instagram):** Facebook Commerce Manager API. OAuth2 via Facebook Login. Product catalog sync via `Catalog Product API`. Orders via `Webhooks` (Meta pushes order events, not a poll API). Requires Business Verification on Meta.
- **TikTok Shop:** TikTok Shop Open Platform API. Separate API credentials from TikTok business account. Listing and order APIs are similar to others but the platform is newer and API stability varies by region.
- **Google Shopping:** Google Merchant Center API (Content API for Shopping). OAuth2 via Google service account. Product listing sync only — not an order channel directly (orders come from Google Ads → own storefront).

**Build priority decision:** Integrating all five channels simultaneously is high complexity. Recommend phasing: Phase 1 = Amazon + one social channel. Use an adapter pattern: `app/Contracts/Ecommerce/ChannelAdapterInterface.php` with methods `pushListing(Product $product)`, `pullOrders(DateTime $since): array`, `updateInventory(ProductVariant $variant, int $quantity)`. Each channel has its own adapter class. Registered via `ChannelAdapterRegistry::register('amazon', AmazonChannelAdapter::class)`.

**API credentials encryption:** `ec_channel_connections.api_credentials` is a JSONB column containing OAuth tokens. Use Laravel's `encrypted` cast: `protected $casts = ['api_credentials' => 'encrypted:array']`. Tokens must be refreshed before expiry — store `token_expires_at` as a separate column for efficient querying by the `RefreshChannelTokensJob` scheduler.

**Inventory sync job:** `SyncChannelInventoryJob` dispatched whenever `inventory_sync` module fires an `InventoryLevelChanged` event. The job iterates all active `ec_channel_listings` for the affected variant and calls the appropriate channel adapter's `updateInventory()` method. Rate-limit: Amazon SP-API has a 5 requests/second throttle per account — use Laravel's rate limiter (`RateLimiter::attempt()`) before each API call.

**AI features:** Listing optimisation and channel mix recommendations call `app/Services/AI/ChannelListingService.php`. The listing optimiser sends current product data (title, description, category, bullet points) to OpenAI GPT-4o with a channel-specific system prompt ("optimise for Amazon A9 algorithm" / "optimise for TikTok Shop discovery"). Returns updated title, bullet points, and description per channel.

## Related

- [[products]] — product catalogue is the source for all channel listings
- [[inventory-sync]] — channel inventory drawn from the same pool
- [[orders]] — marketplace orders pulled into central order management
- [[analytics]] — revenue and performance broken down by channel
