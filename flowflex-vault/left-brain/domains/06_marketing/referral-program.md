---
type: module
domain: Marketing & Content
panel: marketing
cssclasses: domain-marketing
phase: 5
status: planned
migration_range: 400000–449999
last_updated: 2026-05-09
---

# Referral Program Management

Customer-refers-customer referral programs with unique share links, reward tracking, fraud prevention, and automated reward fulfilment. Distinct from the Affiliate module (B2C referrals vs B2B partner referrals). Replaces ReferralHero and Friendbuy.

---

## Features

### Program Builder
- Multiple program types: referral discount, cash reward, store credit, free month
- Referrer reward (person who refers) + referee reward (person who joins)
- One-sided or two-sided reward structures
- Program activation conditions (referee must make purchase, must be new customer)
- Reward cap per customer (prevent gaming)

### Share Mechanics
- Unique referral link per customer
- Personalised share copy ("My friend [name] sent you...")
- One-click share: email, WhatsApp, LinkedIn, Twitter/X, copy link
- Referral widget on client portal and post-purchase page
- QR code for in-person sharing

### Tracking
- Full referral funnel: shared → clicked → signed up → converted
- Attribution window (30/60/90 days)
- Multi-touch attribution (last-touch or first-touch)
- Fraud detection (same device, same IP, referral abuse patterns)

### Reward Management
- Pending → approved → paid status per reward
- Automatic approval trigger (purchase confirmed)
- Manual review queue for suspicious referrals
- Reward fulfilment: voucher code, store credit, Stripe payout, gift card
- Reward batch processing

### Reporting
- Referral program ROI
- Top referrers leaderboard
- Conversion rate by channel
- CAC comparison: referral vs paid ads

---

## Data Model

```erDiagram
    referral_programs {
        ulid id PK
        ulid company_id FK
        string name
        string referrer_reward_type
        decimal referrer_reward_value
        string referee_reward_type
        decimal referee_reward_value
        json conditions
        boolean is_active
    }

    referral_links {
        ulid id PK
        ulid program_id FK
        ulid contact_id FK
        string code
        integer clicks
        integer conversions
    }

    referral_rewards {
        ulid id PK
        ulid link_id FK
        ulid referee_contact_id FK
        string type
        string status
        decimal value
        timestamp earned_at
        timestamp paid_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `ReferralConvertd` | Referee completes purchase | Marketing (trigger reward), Finance (record liability) |
| `ReferralRewardPaid` | Reward issued | Notifications (customer), Finance (record expense) |

---

## Permissions

```
marketing.referrals.view-any
marketing.referrals.manage-programs
marketing.referrals.approve-rewards
```

---

## Competitors Displaced

ReferralHero · Friendbuy · ReferralCandy · Extole · Viral Loops

---

## Related

- [[MOC_Marketing]]
- [[entity-contact]]
- [[MOC_Ecommerce]] — reward fulfilment via store credit/voucher
