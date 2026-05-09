---
tags: [flowflex, domain/marketing, influencer, ugc, creator, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-08
---

# Influencer & UGC Management

Find, manage, and pay influencers and brand ambassadors from one dashboard. Track content submissions, approve UGC for reuse, and measure ROI — without a separate Aspire or CreatorIQ subscription. Works for micro-influencer programmes (10–10,000 followers) up to mid-tier campaigns.

**Who uses it:** Marketing managers, brand teams, social media managers
**Filament Panel:** `marketing`
**Depends on:** Core, [[Contact & Company Management]], [[Social Media Management]], [[Affiliate & Partner Management]], Finance
**Phase:** 5

---

## Features

### Influencer Discovery & Database

- Manual add: add influencer profile (name, platform handles, niche, tier)
- Tiers: Nano (<10k), Micro (10k–100k), Macro (100k–1M), Mega (1M+)
- Tags: niche categories (tech, beauty, sustainability, B2B, etc.)
- Notes + history: relationship notes, past campaigns, content quality rating
- Status: prospect / active / paused / blacklisted
- Import: CSV upload of existing influencer database

### Campaign Management

- Create campaigns with brief, deliverables, timeline, compensation
- Deliverables: Instagram post, Reel, TikTok video, YouTube video, podcast mention, blog review, LinkedIn post
- Assign influencers to campaign: send brief via email directly from platform
- Brief templates: reusable campaign brief with brand guidelines, messaging dos/don'ts, hashtags, links
- Influencer acceptance: influencer confirms participation via link (tracks acceptance)

### Content Submission & Approval

- Submission link: influencer uploads draft content for review
- Review: marketing team approves / requests revision / rejects with feedback
- Revision requests: tracked with comment history
- Final approval: marks content as approved to publish
- Content library: all submitted and approved content stored in File Storage
- UGC rights: mark content as "approved for brand reuse" with rights confirmation

### Content Calendar Sync

- Approved posts linked to [[Social Media Management]] calendar
- Influencer publishes → manager can verify and mark "published"
- Or: brand posts UGC directly from platform using approved content

### Performance Tracking

- Per-campaign stats: total reach, estimated impressions, engagement rate per piece
- UTM link generation: unique tracking links per influencer per campaign
- Traffic from influencer links: click-through rate, conversions, attributed revenue
- ROI: revenue / cost per campaign

### Payments

- Set compensation: flat fee, product-only, commission, hybrid
- Payment workflow: create payout on campaign completion → Finance module approval → Stripe payout or bank transfer
- Payment history per influencer
- Tax: collect W9/W8 (US) or KVK/VAT number (EU) for compliance
- Affiliate overlap: if influencer is also an affiliate → commissions tracked in [[Affiliate & Partner Management]]

---

## Database Tables (3)

### `marketing_influencers`
| Column | Type | Notes |
|---|---|---|
| `crm_contact_id` | ulid FK nullable | if existing CRM contact |
| `name` | string | |
| `email` | string nullable | |
| `tier` | enum | `nano`, `micro`, `macro`, `mega` |
| `platforms` | json | [{platform, handle, follower_count}] |
| `niches` | json | string[] |
| `status` | enum | `prospect`, `active`, `paused`, `blacklisted` |
| `quality_rating` | integer nullable | 1–5 |

### `marketing_influencer_campaigns`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `brief` | text | |
| `start_date` | date | |
| `end_date` | date | |
| `total_budget` | decimal | |
| `status` | enum | `planning`, `active`, `complete` |

### `marketing_influencer_assignments`
| Column | Type | Notes |
|---|---|---|
| `campaign_id` | ulid FK | |
| `influencer_id` | ulid FK | |
| `deliverables` | json | string[] |
| `compensation_type` | enum | `flat_fee`, `product`, `commission`, `hybrid` |
| `compensation_amount` | decimal nullable | |
| `status` | enum | `invited`, `accepted`, `submitted`, `approved`, `paid` |
| `content_file_ids` | json nullable | ulid[] |
| `ugc_rights_granted` | boolean default false | |
| `tracking_url` | string nullable | UTM link |
| `clicks` | integer default 0 | |
| `conversions` | integer default 0 | |
| `attributed_revenue` | decimal default 0 | |

---

## Permissions

```
marketing.influencers.view
marketing.influencers.manage
marketing.influencers.manage-campaigns
marketing.influencers.approve-content
marketing.influencers.process-payments
```

---

## Competitor Comparison

| Feature | FlowFlex | Aspire | CreatorIQ | Grin |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€) | ❌ (€€€) | ❌ (€€€) |
| UGC rights management | ✅ | ✅ | ✅ | ✅ |
| Affiliate overlap handling | ✅ | partial | ❌ | partial |
| CRM contact integration | ✅ | ❌ | ❌ | ❌ |
| Finance payment integration | ✅ | partial | partial | partial |
| EU VAT/KVK compliance | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[Marketing Overview]]
- [[Affiliate & Partner Management]]
- [[Social Media Management]]
- [[AI Content Studio]]
- [[Contact & Company Management]]
