---
type: module
domain: E-Commerce
panel: ecommerce
phase: 3
status: planned
cssclasses: domain-ecommerce
migration_range: 756500–756999
last_updated: 2026-05-09
---

# Marketplace Integration

Sync products and orders between FlowFlex and external marketplaces (Amazon, bol.com, Etsy, FNAC, Zalando). Centralised inventory and order management across all sales channels.

---

## Supported Marketplaces

| Marketplace | Region |
|---|---|
| Amazon | Global |
| bol.com | NL/BE |
| Zalando | EU fashion |
| Etsy | Global handmade |
| FNAC | FR/ES |
| Kaufland | DE |
| Google Shopping | Global |

API connections + marketplace-specific adapters.

---

## Product Listing Sync

Push products from FlowFlex catalogue to marketplaces:
- Map FlowFlex product fields to marketplace-specific fields
- Category mapping (FlowFlex category → Amazon browse node)
- Image upload (marketplace-specific size requirements auto-handled)
- Pricing: same as FlowFlex price or marketplace-specific pricing

---

## Inventory Sync

Single inventory pool:
- Sell on Amazon + own shop + bol.com
- One unit sold anywhere → stock decremented everywhere
- Prevent overselling across channels
- Reserve stock per channel (e.g., keep 10 units for own shop)

---

## Order Consolidation

Orders from all channels arrive in one FlowFlex order queue:
- Marketplace order → FlowFlex order record (normalised format)
- Same fulfilment workflow regardless of channel
- Channel origin tracked (for margin analysis)

---

## Marketplace-Specific Rules

Each marketplace has quirks:
- Amazon FBA: stock sent to Amazon warehouse, Amazon fulfils
- Amazon FBM: fulfilled by merchant (FlowFlex warehouse)
- Zalando: specific label format for returns
- bol.com: specific shipping method requirements

Config per marketplace handles these rules.

---

## Revenue & Fee Reporting

Per marketplace:
- Gross sales revenue
- Marketplace fees (Amazon commission, bol.com referral fee)
- Net revenue after fees
- Compare margin across channels: is Amazon actually profitable?

---

## Data Model

### `ec_marketplace_channels`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| marketplace | varchar(50) | "amazon_eu", "bolcom", etc |
| credentials | json | encrypted |
| status | enum | active/paused/error |

### `ec_channel_orders`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| order_id | ulid | FK FlowFlex order |
| channel_id | ulid | FK |
| marketplace_order_id | varchar(200) | |
| marketplace_fee | decimal(10,2) | nullable |
| net_revenue | decimal(10,2) | |

---

## Migration

```
756500_create_ec_marketplace_channels_table
756501_create_ec_channel_orders_table
756502_create_ec_product_listings_table
```

---

## Related

- [[MOC_Ecommerce]]
- [[warehouse-management]]
- [[lot-batch-serial-tracking]]
- [[MOC_Analytics]] — multi-channel reporting
