---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.referrals
status: planned
color: "#4ADE80"
---

# Referral Program

> Unique referral links per customer, automatic conversion detection, configurable rewards, and a referrer dashboard — a complete customer-to-customer referral program without a separate tool.

**Panel:** `/crm`
**Module key:** `crm.referrals`

## What It Does

Referral Program turns existing customers into a structured acquisition channel. Each customer gets a unique referral link they can share; when a referred prospect signs up and converts to a paying customer, the system detects the conversion (via cookie tracking and UTM attribution), records the referral, and triggers the configured reward (account credit, discount, or cash payout). HR can run bulk invite campaigns to existing customers to seed the programme. A referral leaderboard shows top referrers, and each referrer has a personal dashboard (in the client portal) showing their referrals and earnings.

## Features

### Core
- Referral program creation: define a program with a name, reward type, reward value, reward currency, and cookie window (how many days a referral cookie is valid after a prospect clicks the link)
- Unique referral links: generate one referral link per customer contact (`/r/{slug}`) — the slug is a short unique code derived from the contact's ULID to prevent enumeration
- Click tracking: every click on a referral link is recorded with timestamp; `clicks_count` is denormalised on `crm_referral_links` for fast display
- Referral record creation: when a referred prospect submits a sign-up or contact form on the FlowFlex-hosted landing page, the referral cookie is read and a `crm_referrals` record is created linking the referrer link to the new contact
- Conversion detection: when a referred contact's associated deal moves to Closed Won status in the CRM pipeline, the referral is automatically marked as converted with `converted_at` timestamp

### Advanced
- Reward triggering: on conversion, the system evaluates the reward type: `credit` (creates a credit note on the referrer's account in Finance), `discount` (applies a discount code in the billing engine for the referrer's next invoice), `cash` (creates a payout task in Finance with amount and payee details — manual bank transfer or Stripe Connect payout depending on configuration)
- Referral leaderboard: a ranked list of all referrers by conversion count and total reward value — visible to the company's marketing team; optionally publishable as a public leaderboard
- Bulk referral invite campaign: HR/marketing sends a bulk email to a selected contact segment inviting them to join the referral programme — uses the Email Marketing module for sending; each email contains the recipient's unique referral link pre-populated
- Multiple programmes: a company can run multiple referral programmes simultaneously (e.g. one for customers, one for partners) — each with different reward structures
- UTM attribution fallback: if the referral cookie has expired, UTM parameters (`utm_source=referral&utm_medium=referrer&utm_campaign={slug}`) on the landing page URL are used as a fallback attribution method

### AI-Powered
- Referrer identification: AI analyses the existing customer base and identifies the contacts most likely to be strong referrers based on signals: tenure, product usage breadth, NPS score, and email engagement — surfaces a "Top Referral Candidates" list for outreach prioritisation
- Programme optimisation: after 90 days of programme data, AI compares conversion rate and reward cost by channel and suggests whether to increase reward value, change reward type, or adjust the cookie window based on attribution patterns

## Data Model

```erDiagram
    crm_referral_programs {
        ulid id PK
        ulid company_id FK
        string name
        enum reward_type
        decimal reward_value
        string reward_currency
        integer cookie_days
        boolean is_active
        timestamps created_at/updated_at
    }

    crm_referral_links {
        ulid id PK
        ulid program_id FK
        ulid referrer_contact_id FK
        ulid company_id FK
        string slug
        integer clicks_count
        integer conversions_count
        timestamps created_at/updated_at
    }

    crm_referral_clicks {
        ulid id PK
        ulid link_id FK
        string ip_address_hash
        string user_agent
        string landing_url
        timestamps created_at/updated_at
    }

    crm_referrals {
        ulid id PK
        ulid link_id FK
        ulid company_id FK
        ulid referred_contact_id FK
        timestamp referred_at
        timestamp converted_at "nullable"
        timestamp reward_issued_at "nullable"
        decimal reward_amount "nullable"
        enum reward_status
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `crm_referral_programs.reward_type` | enum: `credit` / `discount` / `cash` / `none` |
| `crm_referral_links.slug` | Unique 8-character alphanumeric code — generated on contact enrolment |
| `crm_referral_clicks.ip_address_hash` | SHA-256 of IP — not stored raw to protect privacy; used to deduplicate clicks |
| `crm_referrals.reward_status` | enum: `pending` / `issued` / `failed` / `waived` |
| `crm_referral_links.clicks_count` | Denormalised counter — incremented via DB `increment()`, not recalculated |

## Permissions

```
crm.referrals.view-programs
crm.referrals.manage-programs
crm.referrals.view-referrals
crm.referrals.issue-rewards
crm.referrals.export-referrals
```

## Filament

- **Resource:** `ReferralProgramResource` — CRUD for programme definitions; includes a "Get Link" column action that copies a contact's referral link to clipboard; shows aggregate stats (total referrals, conversions, rewards issued, programme ROI)
- **Resource:** `ReferralResource` — list of all individual referral records across all programmes; filterable by programme, status (pending / converted / rewarded), date range; includes a "Mark Reward Issued" action for cash payouts
- **Custom page:** `ReferralLeaderboardPage` — ranked table of referrers with conversion count and total reward value; toggle between all-time and last-30-days
- **Relation manager:** `ReferralsRelationManager` on `ContactResource` — shows a contact's referral link, their referral history, and total rewards earned
- **Nav group:** Activities (crm panel)
- **Client portal (Vue 3 + Inertia):** Referrer Dashboard at `/portal/referrals` — shows the logged-in customer their unique link (with copy button and share icons), a table of their referrals with status, and their total rewards earned

## Displaces

| Competitor | Feature Displaced |
|---|---|
| PartnerStack (customer referrals tier) | Customer referral tracking and reward management |
| ReferralHero | Referral programme builder and leaderboard |
| Viral Loops | Referral campaign mechanics and UTM tracking |
| Friendbuy | Customer referral and loyalty reward automation |
| Rewardful | SaaS referral and affiliate tracking |

## Related

- [[contacts]]
- [[deals]]
- [[customer-segments]]
- [[loyalty]]
- [[../marketing/email-marketing]]
- [[../finance/billing-engine]]

## Implementation Notes

### Cookie Tracking
The referral cookie (`_ffref`) is set as a first-party cookie (HttpOnly=false, SameSite=Lax, Secure=true) with a configurable expiry matching `cookie_days`. The cookie value is the referral link slug. On form submission (sign-up, contact, or booking form), the FlowFlex frontend reads `_ffref` from `document.cookie` and includes it as a hidden field in the submission. The backend resolves the slug to a `crm_referral_links` record and creates the `crm_referrals` entry.

If the cookie is absent, check `utm_campaign` in the page URL's query string for UTM fallback attribution.

### Conversion Detection
Conversion is detected by an event listener on the `DealStatusChanged` event in the CRM domain. When `old_status != 'closed_won'` and `new_status == 'closed_won'`, the listener queries `crm_referrals` for a pending referral where `referred_contact_id` is associated with the deal's contact. If found, `converted_at` is set and a `ReferralConversionJob` is dispatched to trigger the reward.

The 30-day conversion window (after `referred_at`) is enforced in the listener — referrals older than `cookie_days` at the time of conversion are not rewarded. This prevents gaming via very old referral attribution.

### Reward Issuance
- **Credit:** call Finance domain's `CreditNoteService::create()` with amount and referrer contact — no manual step required
- **Discount:** call Billing Engine's `ApplyDiscountCode` action — generates a one-time discount code applied to next invoice
- **Cash:** creates a task in the Finance domain's AP queue; a Finance team member reviews and initiates the bank transfer manually, then marks the referral reward as `issued`

Stripe Connect for automated cash payouts is a phase-2 enhancement — V1 uses the manual AP task flow.
