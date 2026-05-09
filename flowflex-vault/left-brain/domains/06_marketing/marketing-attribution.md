---
type: module
domain: Marketing & Demand Gen
panel: marketing
phase: 3
status: planned
cssclasses: domain-marketing
migration_range: 407000–407499
last_updated: 2026-05-09
---

# Marketing Attribution

Track which marketing channels actually drive pipeline and revenue. Multi-touch attribution models. Answer "which campaigns should we invest more in?" with data.

---

## The Attribution Problem

A prospect touched: LinkedIn ad → blog post → webinar → demo request.
Which channel gets credit for the deal?

Different models give different answers — understanding the model is key to making budget decisions.

---

## Attribution Models

| Model | Logic | Best For |
|---|---|---|
| First touch | 100% to first channel | Brand awareness spend |
| Last touch | 100% to channel before conversion | Direct response |
| Linear | Equal split across all touches | Balanced view |
| Time decay | More credit to recent touches | Long sales cycles |
| W-shaped | 40% first, 40% last, 20% distributed | B2B enterprise |
| Custom | Configure weights per touchpoint | Advanced teams |

Switch between models in reporting — same data, different lens.

---

## Touchpoint Capture

Every contact interaction tracked:
- Web visit (UTM source/medium/campaign from [[utm-link-management]])
- Email click (campaign ID)
- Webinar attendance
- Content download (gated asset)
- Direct CRM outreach (SDR email, call)
- Social media click

Anonymous visitor tracking → stitched to contact on form fill.

---

## Revenue Attribution Report

For each closed deal:
- List all marketing touchpoints for all contacts at the company
- Apply attribution model → revenue credit per channel
- Sum across all deals → revenue attributed per channel

**Attributed revenue per channel**:
| Channel | Deals influenced | Revenue | Cost | ROI |
|---|---|---|---|---|
| LinkedIn Ads | 12 | €240k | €18k | 13× |
| SEO/Blog | 8 | €180k | €4k | 45× |
| Events | 5 | €150k | €22k | 6.8× |

---

## Pipeline Attribution

Same as revenue, but for open pipeline:
- Which channels are filling the top of funnel now?
- Leading indicator for revenue in 90 days

---

## Data Model

### `mkt_touchpoints`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| contact_id | ulid | nullable FK |
| session_id | varchar(100) | anonymous visitor |
| channel | varchar(100) | |
| source | varchar(200) | |
| campaign_id | ulid | nullable FK |
| occurred_at | timestamp | |

### `mkt_attribution_results`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| opportunity_id | ulid | FK |
| touchpoint_id | ulid | FK |
| model | varchar(50) | |
| credit | decimal(5,4) | 0–1 |
| attributed_revenue | decimal(14,2) | |

---

## Migration

```
407000_create_mkt_touchpoints_table
407001_create_mkt_attribution_results_table
```

---

## Related

- [[MOC_Marketing]]
- [[utm-link-management]]
- [[landing-page-builder]]
- [[MOC_CRM]] — opportunity data
- [[MOC_Analytics]] — reporting layer
