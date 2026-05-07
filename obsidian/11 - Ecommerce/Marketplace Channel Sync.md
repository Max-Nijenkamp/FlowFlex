---
tags: [flowflex, domain/ecommerce, marketplace, amazon, ebay, phase/4]
domain: Ecommerce
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Marketplace Channel Sync

Manage Amazon, eBay, Etsy, and Shopify listings from one place. Prevent overselling with centralised inventory control across all channels.

**Who uses it:** Ecommerce team, operations managers
**Filament Panel:** `ecommerce`
**Depends on:** [[Product Catalogue]], [[Inventory Management]], [[Order Management]]
**Phase:** 4
**Build complexity:** Very High — 3 resources, 2 pages, 3 tables

---

## Features

- **Multi-channel listing management** — connect Amazon, eBay, Etsy, and Shopify; manage listings for all channels in one Filament resource
- **Product mapping** — map internal `ec_products` to external channel listings; support one-to-many (one product listed on multiple channels)
- **Sync engine** — bidirectional sync of product data (title, description, images, price) and inbound orders; configurable sync frequency per channel
- **Price override per channel** — list at a different price on each channel to account for marketplace fees; without touching the base product price
- **Stock centralisation** — single inventory pool; when a sale on any channel fires, stock is decremented in [[Inventory Management]] to prevent overselling
- **Inbound order ingestion** — marketplace orders are pulled in, crm_contact created/matched, and forwarded to [[Order Management]] as a native order
- **Sync status dashboard** — see all listings with their current sync status: synced/pending/error/unsupported
- **Error handling** — failed syncs logged with error detail; alert sent to ecommerce team; manual retry available
- **Channel sync logs** — full log of every sync run: records processed, errors, duration, timestamp
- **`ChannelSyncFailed` alert** — notifies ecommerce team when a sync run fails or error rate exceeds threshold
- **Credential management** — store API credentials per channel with `encrypted` cast; test connection button validates credentials
- **Shopify webhook ingestion** — receive real-time order and stock webhooks from Shopify for near-instant sync

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `marketplace_channels`
| Column | Type | Notes |
|---|---|---|
| `name` | string | display name e.g. "Amazon UK" |
| `type` | enum | `amazon`, `ebay`, `etsy`, `shopify` |
| `credentials` | json (encrypted) | API keys/tokens — encrypted cast |
| `is_active` | boolean default true | |
| `last_synced_at` | timestamp nullable | |
| `sync_frequency_minutes` | integer default 60 | |
| `settings` | json nullable | channel-specific config |

### `channel_listings`
| Column | Type | Notes |
|---|---|---|
| `marketplace_channel_id` | ulid FK | → marketplace_channels |
| `ec_product_id` | ulid FK | → ec_products |
| `ec_product_variant_id` | ulid FK nullable | → ec_product_variants |
| `external_id` | string | channel's product/listing ID |
| `external_url` | string nullable | URL on the marketplace |
| `sync_status` | enum | `synced`, `pending`, `error`, `unsupported` |
| `last_synced_at` | timestamp nullable | |
| `price_override` | decimal(10,2) nullable | channel-specific price |
| `error_message` | text nullable | |
| `is_active` | boolean default true | |

### `channel_sync_logs`
| Column | Type | Notes |
|---|---|---|
| `marketplace_channel_id` | ulid FK | → marketplace_channels |
| `type` | enum | `product`, `order`, `stock` |
| `status` | enum | `success`, `partial`, `failed` |
| `records_processed` | integer default 0 | |
| `records_failed` | integer default 0 | |
| `errors` | json nullable | array of error messages |
| `synced_at` | timestamp | |
| `duration_ms` | integer nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ChannelSyncCompleted` | `channel_id`, `records_processed` | Activity log |
| `ChannelSyncFailed` | `channel_id`, `error_count` | Notification to ecommerce team |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `InventoryStockUpdated` | [[Inventory Management]] | Triggers stock level push to all active channels with listings for that product |
| `ECProductUpdated` | [[Product Catalogue]] | Triggers product data sync to all active channel listings |

---

## Permissions

```
ecommerce.marketplace-channels.view
ecommerce.marketplace-channels.create
ecommerce.marketplace-channels.edit
ecommerce.marketplace-channels.delete
ecommerce.marketplace-channels.sync
ecommerce.channel-listings.view
ecommerce.channel-listings.create
ecommerce.channel-listings.edit
ecommerce.channel-listings.delete
ecommerce.channel-sync-logs.view
```

---

## Related

- [[Ecommerce Overview]]
- [[Product Catalogue]]
- [[Order Management]]
- [[Inventory Management]]
