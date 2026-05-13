---
type: module
domain: Partner & Channel
panel: partners
module-key: partners.affiliates
status: planned
color: "#4ADE80"
---

# Affiliate Management

> Unique referral links per affiliate, first-party cookie tracking (30-day window), click and conversion analytics, automated commission triggering on first payment, leaderboard, and fraud detection (same-IP, click-stuffing).

**Panel:** `/partners`
**Module key:** `partners.affiliates`

## What It Does

Affiliate Management provides internal referral link tracking for partners classified as `affiliate` type. Each affiliate partner receives one or more unique tracking links they share with their audience. When a visitor clicks a link, a first-party cookie and UTM parameters are stored. If the visitor converts (signs up and makes their first payment), the conversion is attributed to the affiliate, and a commission is automatically triggered via the Partner Commissions module. The entire tracking infrastructure is first-party — no external affiliate network is required. The system includes fraud detection to identify suspicious patterns like conversions from the same IP as clicks or abnormally high click-to-conversion ratios that indicate click stuffing.

## Features

### Core
- Unique referral link generation per affiliate: short slug-based URLs (e.g. `https://app.flowflex.io/ref/{slug}`) redirect to a configurable landing page. Each link stores the affiliate's partner ULID in a signed first-party cookie and appends `?utm_source={slug}&utm_medium=affiliate` to the destination URL.
- 30-day attribution window: first-party cookie with 30-day expiry. If the visitor returns within 30 days and converts, the conversion is attributed to the original affiliate click.
- Click tracking: every visit to a referral link is recorded in `affiliate_link_clicks` with timestamp, IP hash (SHA-256), user-agent hash, and referrer URL.
- Conversion tracking: on first payment event (from Subscription Billing domain), `AffiliateConversionTracker` service checks the session/cookie for an active referral attribution and creates an `affiliate_conversions` record.
- Automated commission trigger: on conversion recorded, dispatches `AffiliateConversionRecorded` event. Commission module listener creates a `partner_commissions` record based on the matching affiliate commission rule.
- Multiple links per affiliate: affiliates can have multiple tracking links targeting different landing pages (e.g. one for homepage, one for a specific product page). Each link has its own click and conversion metrics.

### Advanced
- Affiliate dashboard in partner portal: affiliate partners see their referral links (copy to clipboard), click count, conversion count, conversion rate, commissions earned this month, and a performance chart.
- Affiliate leaderboard: optional leaderboard in the portal showing all affiliates ranked by conversions or commissions this quarter. Company configures visibility (named / anonymised / hidden).
- Custom landing page URLs per link: instead of redirecting to the company homepage, an affiliate link can target any specific page (e.g. a dedicated affiliate landing page). Configured in Filament per link.
- Conversion analytics: funnel view (clicks → sign-ups → first payment → LTV) per affiliate and per link. LTV pulled from the Subscription Billing domain.
- Sub-IDs: affiliates can append sub-IDs to their link (`/ref/{slug}?sub=youtube_video_1`) for granular tracking. Sub-ID stored on click and conversion records for affiliate-side reporting.
- Affiliate-specific landing page URLs: company can create dedicated landing pages per affiliate in the Marketing domain and link them to an affiliate's referral link for branded experiences.

### AI-Powered
- Fraud risk scoring: Claude + rule-based checks analyse each conversion record before creating the commission: same IP as the click (high risk), conversion within 30 seconds of first click (high risk), conversion rate > 50% on a link with > 100 clicks (statistical anomaly). Risk score surfaced in Filament. High-risk conversions require manual review before commission is created.
- Affiliate quality tiers: AI analyses each affiliate's click quality (conversion rate, average LTV of referred customers, fraud score) and suggests reclassifying low-quality affiliates for reduced commission rates or removal

## Data Model

```erDiagram
    affiliate_links {
        ulid id PK
        ulid company_id FK
        ulid partner_id FK
        string slug
        string destination_url
        string name
        integer clicks_count
        integer conversions_count
        boolean is_active
        timestamps created_at/updated_at
    }

    affiliate_link_clicks {
        ulid id PK
        ulid link_id FK
        string ip_hash
        string user_agent_hash
        string referrer_url
        string sub_id
        timestamp clicked_at
    }

    affiliate_conversions {
        ulid id PK
        ulid company_id FK
        ulid link_id FK
        ulid contact_id FK
        timestamp converted_at
        decimal deal_value
        string currency
        decimal commission_earned
        string ip_hash
        string cookie_id
        integer fraud_risk_score
        boolean requires_manual_review
        boolean is_approved
        ulid commission_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `slug` | Globally unique within the company. URL-safe alphanumeric, 8–12 characters. Auto-generated on link creation; can be customised. |
| `ip_hash` | SHA-256 of raw IP address — never store raw IP for GDPR compliance |
| `user_agent_hash` | SHA-256 of user agent string |
| `cookie_id` | UUID from the first-party cookie set on click — used to link the conversion back to the originating click |
| `fraud_risk_score` | 0–100. 0 = clean; > 70 = high risk. Combination of rule checks and AI scoring. |
| `requires_manual_review` | set true when `fraud_risk_score >= 70` — commission not created until reviewed |
| `commission_id` | FK to `partner_commissions` — set after commission is created (null until approved) |

## Permissions

```
partners.affiliates.view
partners.affiliates.create-links
partners.affiliates.review-conversions
partners.affiliates.fraud-manage
partners.affiliates.export
```

## Filament

- **Resource:** `AffiliateResource` — list of affiliate partners with total clicks, total conversions, conversion rate, commissions earned columns. `AffiliateConversionResource` — list of all conversions with fraud risk score column (colour-coded), review status. High-risk conversions highlighted. Review action: approve (triggers commission creation) or reject (marks conversion invalid).
- **Pages:** `ListAffiliates`, `ViewAffiliate` (partner detail with link management sub-table and performance chart), `ListAffiliateConversions`, `ViewAffiliateConversion`
- **Custom pages:** `AffiliateLinkBuilderPage` — create and manage links for a specific affiliate. Slug generator, destination URL picker, sub-ID documentation. Class: `App\Filament\Partners\Pages\AffiliateLinkBuilderPage`. `AffiliateLeaderboardPage` — view leaderboard with configurable time period and metric. Class: `App\Filament\Partners\Pages\AffiliateLeaderboardPage`.
- **Widgets:** `TopAffiliatesWidget` (top 5 by conversions this month), `AffiliateConversionsTodayWidget`, `FraudReviewQueueWidget` (count of conversions pending manual review) — on Partners panel dashboard
- **Nav group:** Resources (partners panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| PartnerStack | Affiliate tracking and commissions |
| Impact.com | Affiliate network management |
| Tapfiliate | Affiliate tracking software |
| Refersion | Affiliate management platform |
| Post Affiliate Pro | Referral and affiliate tracking |

## Related

- [[partner-portal]]
- [[partner-commissions]]
- [[deal-registration]]
- [[domains/crm/contacts]]
- [[domains/subscription-billing/INDEX]]
- [[domains/marketing/INDEX]]

## Implementation Notes

- **Referral link redirect:** `/ref/{slug}` handled by `AffiliateRedirectController`. On hit: increments `affiliate_links.clicks_count`, creates `affiliate_link_clicks` record (async via queued job to avoid redirect latency), sets first-party cookie `_ffref` with value = link ULID and expiry = 30 days, and issues a 302 redirect to `destination_url`. Cookie is `SameSite=Lax; Secure; HttpOnly=false` (needs to be readable for sub-ID appending by JS if used). All in < 50ms target response time.
- **Conversion detection:** Middleware `TrackAffiliateConversion` runs on the payment success route (from Subscription Billing domain). Reads `_ffref` cookie → looks up `affiliate_links` record → calls `AffiliateConversionTracker::record($linkId, $contactId, $dealValue)`. Service creates `affiliate_conversions` record and dispatches `CalculateAffiliateConversionFraudRisk` queued job.
- **Fraud scoring:** `FraudRiskCalculator` service runs as a queued job. Checks: (1) IP hash match with any `affiliate_link_clicks` from the same link (same-IP click and convert = +50 points). (2) Time delta between `clicked_at` and `converted_at` < 60 seconds (+30 points). (3) Conversion rate on the link > 40% with > 50 clicks (+20 points, statistical anomaly). Score summed and stored. If > 70: sets `requires_manual_review = true` and dispatches `FraudReviewRequired` notification to partner manager.
- **Commission trigger:** When `affiliate_conversions.is_approved` is set to true (either automatically for low-risk or manually by reviewer), `AffiliateConversionApprovedListener` creates a `partner_commissions` record (delegating to the same `CommissionCalculator` service used by deal registration conversions, with conversion source = affiliate).
- **GDPR compliance:** Raw IP addresses are never stored — only SHA-256 hashes. The `_ffref` cookie is first-party and does not cross domains, so no third-party cookie consent required under GDPR's current interpretation. Cookie banner on the marketing site should mention referral tracking in the functional cookies category. Cookie lifetime configurable (default 30 days).
