---
type: module
domain: Marketing & Demand Gen
panel: marketing
phase: 3
status: planned
cssclasses: domain-marketing
migration_range: 406500–406999
last_updated: 2026-05-09
---

# Affiliate Program

Run a partner/affiliate program. Affiliates get unique tracking links, earn commission on referred sales, and access a self-service portal for their performance data.

---

## Program Setup

Configure program rules:
- Commission model: flat per sale, % of revenue, tiered by volume
- Attribution window: last-click 30 days (configurable)
- Cookie tracking: first-party cookie + server-side fallback
- Eligible products / excluded products
- Payment threshold (minimum to trigger payout)
- Payment method: bank transfer, PayPal, gift card

---

## Affiliate Onboarding

Affiliates apply via public signup page:
- Application form: website, audience size, content type
- Manual review or auto-approve
- On approval: affiliate receives unique tracking links + access to portal

---

## Tracking

Tracking link: `company.com/ref/affiliate-slug?utm_source=affiliate`

Click recorded → cookie set → on purchase: affiliate credited if within attribution window.

Server-side tracking as fallback (handles ad blockers, iOS privacy changes).

---

## Affiliate Portal

Self-service dashboard:
- Clicks, conversions, conversion rate
- Earnings: pending, approved, paid
- Generate new tracking links (per campaign/product)
- Download creative assets (banners, copy)
- Payment history + payout schedule

---

## Commission Lifecycle

```
Sale attributed → Commission pending (30-day validation window for refunds)
→ Approved → Added to next payout run → Paid
→ Refund before window: commission reversed
```

---

## Multi-Tier (MLM-style, optional)

Support for sub-affiliate programs:
- Affiliate A recruits Affiliate B
- Affiliate A earns 2% override on B's conversions

Configurable depth (1 or 2 tiers). Compliance flags for jurisdictions with MLM regulations.

---

## Data Model

### `mkt_affiliates`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| email | varchar(300) | |
| tracking_slug | varchar(100) | unique |
| status | enum | pending/active/suspended |
| commission_plan_id | ulid | FK |

### `mkt_affiliate_conversions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| affiliate_id | ulid | FK |
| order_id | ulid | FK |
| gross_value | decimal(14,2) | |
| commission_amount | decimal(14,2) | |
| status | enum | pending/approved/paid/reversed |

---

## Migration

```
406500_create_mkt_affiliates_table
406501_create_mkt_affiliate_clicks_table
406502_create_mkt_affiliate_conversions_table
406503_create_mkt_affiliate_payouts_table
```

---

## Related

- [[MOC_Marketing]]
- [[referral-program]]
- [[utm-link-management]]
