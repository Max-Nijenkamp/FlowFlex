---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.referrals
status: planned
priority: v1
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications]
soft-depends: [crm.deals, ecommerce.promotions]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [crm_referral_programs, crm_referrals]
permission-prefix: crm.referrals
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Referral Program

Customer referral tracking with unique links, conversion detection, and configurable rewards. Turn customers into a referral channel.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | referrers + referees are contacts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, reward notifications |
| Soft | [[domains/crm/deals\|crm.deals]] | qualifying conversion = referee deal won *(assumed)* |
| Soft | [[domains/ecommerce/promotions\|ecommerce.promotions]] | discount-code reward fulfilment (P3) |

---

## Core Features

- Referral program: name, reward structure, terms, active period
- Unique referral link/code per customer (referrer)
- Referral tracking: link click → signup → conversion attribution (code captured on form/contact source)
- Reward types: discount, credit, cash, gift — to referrer and/or referee
- Reward fulfilment: trigger reward on qualifying conversion (**v1: manual fulfilment task + notification; automated fulfilment via promotions/finance later** *(assumed)*)
- Referral leaderboard (gamification)
- Fraud detection: self-referral (same email/contact), duplicate prevention
- Referral status: `pending → qualified → rewarded`

---

## Data Model

### crm_referral_programs

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| referrer_reward / referee_reward | jsonb | {type, value_cents/percent, note} |
| terms | text | |
| is_active | boolean | |
| starts_at / ends_at | date nullable | |
| deleted_at | timestamp nullable | |

### crm_referrals

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), program_id FK | ulid | |
| referrer_contact_id | ulid FK crm_contacts | |
| referral_code | string | unique `(company_id, referral_code)` |
| referee_email | string | unique `(program_id, referee_email)` — duplicate guard |
| referee_contact_id | ulid nullable FK | linked on signup |
| status | string default `pending` | pending / qualified / rewarded / rejected |
| converted_at / rewarded_at | timestamp nullable | |

---

## DTOs

### CreateProgramData — name, referrer_reward/referee_reward {type in:discount,credit,cash,gift, value}, starts_at/ends_at (end after start), terms
### RegisterReferralData — referral_code (valid, active program), referee_email (not referrer's own — "Self-referrals are not allowed."; unique per program)

## Services & Actions

- `ReferralService::codeFor(string $contactId, string $programId): string` — generate-or-return
- `ReferralService::register(RegisterReferralData $data): ReferralData` — fraud checks (self-referral by email/contact match, duplicate)
- `ReferralService::qualify(string $referralId): ReferralData` — on conversion (manual or deal-won check); notifies fulfilment owner
- `ReferralService::markRewarded(string $referralId): ReferralData`
- `ReferralService::leaderboard(string $programId): Collection`

---

## Filament

**Nav group:** Intelligence

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ReferralProgramResource` | #1 CRUD resource | reward config |
| `ReferralResource` | #1 CRUD resource | status pipeline, qualify/reward actions |
| `ReferralLeaderboardPage` | #9 report custom page | top referrers |

---

## Permissions

`crm.referrals.view-any` · `crm.referrals.manage-programs` · `crm.referrals.qualify` · `crm.referrals.reward`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Self-referral rejected (email + contact match)
- [ ] Duplicate referee per program rejected
- [ ] Code registration only within active program window
- [ ] Qualify → notification to fulfilment owner; rewarded stamps
- [ ] Leaderboard counts qualified+rewarded only *(assumed)*

---

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

---

## Related

- [[domains/crm/contacts]]
- [[domains/ecommerce/promotions]]
