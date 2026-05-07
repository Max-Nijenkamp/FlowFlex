---
tags: [flowflex, domain/marketing, ads, campaigns, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Ad Campaign Management

Connect ad accounts across Google, Meta, LinkedIn, and TikTok. See spend, ROAS, and conversion performance in one unified view — without switching between platforms.

**Who uses it:** Marketing team, paid media managers, CMO
**Filament Panel:** `marketing`
**Depends on:** Core (read-only pull from ad platform APIs)
**Phase:** 5
**Build complexity:** Medium — 3 resources, 2 pages, 3 tables

---

## Features

- **Multi-platform account connection** — connect Google Ads, Meta Ads (Facebook + Instagram), LinkedIn Campaign Manager, and TikTok Ads via OAuth/API credentials stored encrypted
- **Campaign overview dashboard** — all active campaigns from all platforms in one table: platform, name, status, spend, impressions, clicks, conversions, ROAS
- **Daily performance snapshots** — pull performance data daily per campaign and store in `ad_performance_snapshots`; ensures data is available even when API rate limits apply
- **Spend vs ROAS chart** — scatter or line chart showing spend vs return over time; identify which campaigns deliver most efficiency
- **Budget pacing widget** — for each campaign, show % of budget consumed vs % of campaign period elapsed; surface over/underpacing campaigns
- **`CampaignBudgetExhausted` alert** — notify marketing manager when a campaign's spend reaches 100% of budget mid-flight
- **Cross-platform attribution view** — compare total conversions across channels; note: last-click by default, multi-touch deferred
- **Campaign period reporting** — filter performance snapshots by custom date range; aggregate across all platforms
- **ROAS threshold alerts** — set a minimum ROAS threshold per campaign; notify if performance drops below it
- **Conversion tracking** — import conversion counts from each platform; link to CRM deals if available
- **Export to CSV/PDF** — export campaign performance report for client or leadership reporting
- **Credential health check** — test connection status for each ad account; alert if credentials have expired or been revoked

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `ad_accounts`
| Column | Type | Notes |
|---|---|---|
| `platform` | enum | `google`, `meta`, `linkedin`, `tiktok` |
| `name` | string | display name e.g. "Google Ads – Brand" |
| `account_id` | string | platform's account/customer ID |
| `credentials` | json (encrypted) | OAuth tokens / API keys — encrypted cast |
| `is_active` | boolean default true | |
| `last_synced_at` | timestamp nullable | |
| `connection_status` | enum | `connected`, `error`, `disconnected` |

### `ad_campaigns`
| Column | Type | Notes |
|---|---|---|
| `ad_account_id` | ulid FK | → ad_accounts |
| `external_campaign_id` | string | platform's campaign ID |
| `name` | string | |
| `platform` | enum | mirrored from account for easy filter |
| `status` | enum | `active`, `paused`, `ended`, `draft` |
| `budget` | decimal(10,2) nullable | total or daily budget |
| `budget_type` | enum | `daily`, `lifetime` |
| `spend` | decimal(10,2) default 0 | lifetime spend |
| `impressions` | bigint default 0 | |
| `clicks` | integer default 0 | |
| `conversions` | integer default 0 | |
| `roas` | decimal(8,4) nullable | revenue / spend |
| `period_start` | date nullable | |
| `period_end` | date nullable | |
| `last_synced_at` | timestamp nullable | |

### `ad_performance_snapshots`
| Column | Type | Notes |
|---|---|---|
| `ad_campaign_id` | ulid FK | → ad_campaigns |
| `date` | date | |
| `spend` | decimal(10,2) | |
| `impressions` | integer | |
| `clicks` | integer | |
| `conversions` | integer | |
| `roas` | decimal(8,4) nullable | |
| `cpc` | decimal(8,4) nullable | cost per click |
| `ctr` | decimal(5,4) nullable | click-through rate |
| `cpa` | decimal(10,2) nullable | cost per acquisition |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `CampaignBudgetExhausted` | `ad_campaign_id`, `platform`, `spend` | Notification to marketing manager |

---

## Events Consumed

None — Ad Campaign Management is a pull-only reporting module.

---

## Permissions

```
marketing.ad-accounts.view
marketing.ad-accounts.create
marketing.ad-accounts.edit
marketing.ad-accounts.delete
marketing.ad-campaigns.view
marketing.ad-campaigns.sync
marketing.ad-performance-snapshots.view
marketing.ad-reports.view
marketing.ad-reports.export
```

---

## Related

- [[Marketing Overview]]
- [[SEO & Analytics]]
- [[Email Marketing]]
