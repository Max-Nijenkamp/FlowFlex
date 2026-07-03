---
domain: crm
module: referral-program
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# CRM Referral Program

Customer referral tracking with unique links, conversion detection, and configurable rewards. Turns customers into a referral channel.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

`crm.referrals`

**Priority:** v1  
**Panel:** crm  
**Permission prefix:** `crm.referrals`  
**Tables:** `crm_referral_programs`, `crm_referrals`

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|Contacts]] | Referrers and referees are contacts. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating. |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permission enforcement. |
| Hard | [[../../../infrastructure/mail\|core.notifications]] | Reward notifications. |
| Soft | [[../../crm/deals/_module\|Deals]] | Qualifying conversion = referee deal won *(assumed)*. |
| Soft | [[../../ecommerce/promotions/_module\|Promotions]] | Discount-code reward fulfilment (P3). |

## Core Features

- Referral program — name, reward structure, terms, active period.
- Unique referral link / code per customer (referrer).
- Referral tracking — link click → signup → conversion attribution; code captured on form / contact source.
- Reward types — discount, credit, cash, gift — to referrer and/or referee.
- Reward fulfilment — trigger reward on qualifying conversion; v1 is a manual fulfilment task + notification, automated fulfilment via promotions / finance is later *(assumed)*.
- Referral leaderboard — gamification.
- Fraud detection — self-referral (same email / contact), duplicate prevention.
- Referral status — pending → qualified → rewarded.

See [[features/referral-tracking]] and [[features/reward-fulfilment]] for the flows.

## Build Manifest

```
database/migrations/xxxx_create_crm_referral_programs_table.php
database/migrations/xxxx_create_crm_referrals_table.php
app/Models/CRM/{ReferralProgram,Referral}.php
app/Data/CRM/{CreateProgramData,RegisterReferralData,ReferralData}.php
app/Services/CRM/ReferralService.php
app/Filament/CRM/Resources/{ReferralProgramResource,ReferralResource}.php
app/Filament/CRM/Pages/ReferralLeaderboardPage.php
database/factories/CRM/{ReferralProgramFactory,ReferralFactory}.php
tests/Feature/CRM/{ReferralFraudTest,ReferralLifecycleTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see/qualify/reward company B referrals
- [ ] Module gating: artifacts hidden when `crm.referrals` inactive
- [ ] Self-referral rejected (email + contact match).
- [ ] Duplicate referee per program rejected.
- [ ] Code registration only within an active program window.
- [ ] Qualify → notification to fulfilment owner; rewarded stamps.
- [ ] Leaderboard counts qualified + rewarded only *(assumed)*.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | Contact read API | [[../contacts/_module\|crm.contacts]] | Referrers and referees are contacts. |
| Consumes | `DealWon` | [[../deals/_module\|crm.deals]] *(assumed)* | Qualifies a referral (referee deal won). |
| Fires | `ReferralQualified` | core.notifications | Notify fulfilment owner. |
| Fires | `ReferralRewarded` / reward events | crm.referrals (self) / finance | Reward stamped. |
| Feeds | credit / payout *(assumed)* | finance | Cash/credit reward fulfilment (later). |
| Feeds | discount code (P3) | [[../../ecommerce/promotions/_module\|ecommerce.promotions]] | Discount-code reward. |

Public capture route feeds referral registration (unauthenticated) *(assumed — under-specified)*.

**Data ownership:** `referral-program` writes only `crm_referral_programs`, `crm_referrals`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../contacts/_module|Contacts]]
- [[../../ecommerce/promotions/_module|Promotions]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
