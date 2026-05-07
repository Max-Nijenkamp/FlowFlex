---
tags: [flowflex, domain/ecommerce, digital-products, downloads, phase/4]
domain: Ecommerce
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Digital Products & Downloads

Deliver digital files automatically after purchase. No manual fulfilment — secure download links generated and emailed to customers the moment payment clears.

**Who uses it:** Ecommerce team, customers (download portal)
**Filament Panel:** `ecommerce`
**Depends on:** [[Product Catalogue]], [[Order Management]], [[File Storage]]
**Phase:** 4
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **Automatic delivery** — on order completion, `DownloadLinkGenerated` event fires and emails a secure download link to the customer; no manual action required
- **Secure signed URLs** — download links are time-limited S3 presigned URLs wrapped in a token stored in `download_links`; never expose raw S3 paths
- **Download limits** — configurable maximum downloads per purchase (e.g. 5 downloads); counter tracked per link; `downloads_used` incremented on each access
- **Link expiry** — configurable expiry period per product (e.g. 30 days); after expiry the link returns 403 and customer is prompted to contact support
- **Link revocation** — admin can revoke any download link (e.g. charge-back, refund); `is_revoked` flag checked on every access attempt
- **Licence key management** — products can have a pool of unique licence keys stored encrypted; on purchase, one key is assigned from the pool and delivered in the download email
- **Streaming support flag** — mark a product as streaming (e.g. video course); fulfilment delivers a time-limited playback URL instead of a download
- **Download portal** — customer-facing download page (public, token-authenticated) showing purchased files, remaining downloads, and expiry date
- **Multiple files per product** — a single digital product can include multiple files (e.g. software + documentation + licence); all delivered together
- **Re-send download email** — admin can resend the download email for any order from the Filament resource
- **Licence key pool management** — view assigned/unassigned keys; import key lists in bulk; alert when pool is running low
- **Access logs** — log every download attempt with IP, user-agent, and timestamp for fraud detection

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `digital_products`
| Column | Type | Notes |
|---|---|---|
| `ec_product_id` | ulid FK | → ec_products |
| `file_id` | ulid FK nullable | → files (primary download file) |
| `additional_file_ids` | json nullable | array of file IDs for multi-file delivery |
| `download_limit` | integer nullable | null = unlimited |
| `expiry_days` | integer nullable | days after purchase before link expires |
| `is_streaming` | boolean default false | deliver playback URL instead of download |
| `licence_key_required` | boolean default false | |

### `download_links`
| Column | Type | Notes |
|---|---|---|
| `order_id` | ulid FK | → orders |
| `digital_product_id` | ulid FK | → digital_products |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `token` | string unique | cryptographically random |
| `downloads_used` | integer default 0 | |
| `download_limit` | integer nullable | snapshot from product at time of purchase |
| `expires_at` | timestamp nullable | |
| `is_revoked` | boolean default false | |
| `revoked_at` | timestamp nullable | |
| `revoked_by` | ulid FK nullable | → tenants |
| `last_downloaded_at` | timestamp nullable | |

### `licence_keys`
| Column | Type | Notes |
|---|---|---|
| `digital_product_id` | ulid FK | → digital_products |
| `key` | string (encrypted) | encrypted cast — never log |
| `is_assigned` | boolean default false | |
| `order_id` | ulid FK nullable | → orders (when assigned) |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `assigned_at` | timestamp nullable | |
| `expires_at` | timestamp nullable | |
| `is_revoked` | boolean default false | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `DownloadLinkGenerated` | `download_link_id`, `order_id`, `crm_contact_id` | Email notification to customer with secure download link |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `OrderCompleted` | [[Order Management]] | For each digital product line item, generate a `download_link`, assign a `licence_key` if required, and fire `DownloadLinkGenerated` |

---

## Permissions

```
ecommerce.digital-products.view
ecommerce.digital-products.create
ecommerce.digital-products.edit
ecommerce.digital-products.delete
ecommerce.download-links.view
ecommerce.download-links.revoke
ecommerce.download-links.resend
ecommerce.licence-keys.view
ecommerce.licence-keys.create
ecommerce.licence-keys.import
ecommerce.licence-keys.revoke
```

---

## Related

- [[Ecommerce Overview]]
- [[Order Management]]
- [[Product Catalogue]]
- [[File Storage]]
