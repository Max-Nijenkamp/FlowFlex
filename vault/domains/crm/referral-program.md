---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.referrals
status: planned
color: "#4ADE80"
---

# Referral Program

Customer referral tracking with unique links, conversion detection, and configurable rewards. Turn customers into a referral channel.

## Core Features

- Referral program: name, reward structure, terms, active period
- Unique referral link/code per customer (referrer)
- Referral tracking: link click → signup → conversion attribution
- Reward types: discount, credit, cash, gift — to referrer and/or referee
- Reward fulfilment: trigger reward on qualifying conversion
- Referral leaderboard (gamification)
- Fraud detection: self-referral, duplicate prevention
- Referral status: pending → qualified → rewarded

## Data Model

| Table | Key Columns |
|---|---|
| `crm_referral_programs` | company_id, name, referrer_reward (json), referee_reward (json), terms, is_active |
| `crm_referrals` | company_id, program_id, referrer_contact_id, referral_code, referee_email, status, converted_at, rewarded_at |

## Filament

**Nav group:** Intelligence

- `ReferralProgramResource` — configure programs + rewards
- `ReferralResource` — track referrals, status, fulfil rewards
- `ReferralLeaderboardPage` (custom page) — top referrers

## Cross-Domain / Events

- Conversion detection links to new contacts/deals
- Reward fulfilment may create discount codes (E-commerce) or credits (Finance)

## Related

- [[domains/crm/contacts]]
- [[domains/ecommerce/promotions]]
