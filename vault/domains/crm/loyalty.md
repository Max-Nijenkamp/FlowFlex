---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.loyalty
status: planned
color: "#4ADE80"
---

# Loyalty

> Customer loyalty program management — points accrual, tier progression, rewards catalog, redemption, and loyalty analytics.

**Panel:** `crm`
**Module key:** `crm.loyalty`

## What It Does

The Loyalty module enables companies to run a branded customer loyalty program. Customers earn points for purchases, referrals, reviews, and other configured actions. Points accumulate toward tier thresholds (Bronze, Silver, Gold, Platinum) that unlock increasingly valuable rewards. The rewards catalog defines what points can be redeemed for — discounts, free products, exclusive access. HR and CRM teams can manually award bonus points for special occasions. Loyalty analytics show program engagement, points liability, and redemption trends.

## Features

### Core
- Points rules: configure events that earn points — purchase (per € spent), referral (flat bonus), review submission, renewal — with configurable point values
- Tier structure: define tiers with names, minimum point thresholds, and tier benefits (e.g. Silver = 10% discount on all orders, priority support)
- Customer loyalty record: current points balance, tier, points history, redemption history, tier anniversary date
- Rewards catalog: list of rewards with point cost — discount vouchers, free products, event tickets, merchandise
- Redemption: customer (or rep on their behalf) redeems points for a reward — points deducted, reward fulfillment triggered

### Advanced
- Points expiry: configurable expiry (e.g. points expire 12 months after earning) — expiry reminder notification sent to customer 30 days before
- Manual bonus points: admin awards bonus points to a customer with a reason (e.g. "Customer appreciation for 5-year anniversary")
- Tier downgrade protection: configurable grace period before a customer who falls below the tier threshold is downgraded (e.g. 90-day review window)
- Points liability reporting: total outstanding unredeemed points × redemption cost — a financial liability tracked in Finance
- Program communication: automated email notifications on tier change, points earned, and points expiry

### AI-Powered
- Redemption propensity: AI identifies customers close to a reward threshold and triggers a targeted "You're X points away from your next reward!" nudge
- Churn risk from loyalty drop-off: customers who were active in the loyalty program and have stopped earning points flagged as potential churn risks to the account team

## Data Model

```erDiagram
    loyalty_programs {
        ulid id PK
        ulid company_id FK
        string name
        json tier_config
        json points_rules
        boolean is_active
        timestamps created_at/updated_at
    }

    loyalty_accounts {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        integer points_balance
        string current_tier
        timestamp tier_achieved_at
        timestamps created_at/updated_at
    }

    loyalty_transactions {
        ulid id PK
        ulid loyalty_account_id FK
        ulid company_id FK
        string type
        integer points
        string description
        timestamp expires_at
        ulid created_by FK
        timestamps created_at/updated_at
    }

    loyalty_rewards {
        ulid id PK
        ulid company_id FK
        string name
        string description
        integer points_cost
        string reward_type
        boolean is_active
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | earned / redeemed / expired / bonus / adjustment |
| `reward_type` | discount / product / experience / other |
| `tier_config` | JSON array of tier definitions with thresholds and benefits |

## Permissions

- `crm.loyalty.view`
- `crm.loyalty.manage-program`
- `crm.loyalty.award-points`
- `crm.loyalty.manage-rewards`
- `crm.loyalty.view-analytics`

## Filament

- **Resource:** `LoyaltyRewardResource`, `LoyaltyAccountResource`
- **Pages:** `ListLoyaltyRewards`, `ListLoyaltyAccounts`, `ViewLoyaltyAccount` (with transaction history)
- **Custom pages:** `LoyaltyAnalyticsPage` — enrollment, points earned vs redeemed, tier distribution
- **Widgets:** `LoyaltyEngagementWidget` — active loyalty members and total points liability on CRM dashboard
- **Nav group:** Intelligence (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Yotpo Loyalty | Customer loyalty and rewards program |
| LoyaltyLion | Ecommerce loyalty program |
| Antavo | Enterprise loyalty management |
| Smile.io | Points and rewards program |

## Related

- [[contacts]]
- [[deals]]
- [[customer-segments]]
- [[revenue-intelligence]]
