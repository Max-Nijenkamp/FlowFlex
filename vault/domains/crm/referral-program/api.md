---
domain: crm
module: referral-program
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Referral Program — API & DTOs

## Input DTOs

### CreateProgramData

| Field | Type | Rules |
|---|---|---|
| name | string | Required. |
| referrer_reward | object | `{type in: discount,credit,cash,gift, value}`. |
| referee_reward | object | `{type in: discount,credit,cash,gift, value}`. |
| starts_at | date | |
| ends_at | date | Must be after `starts_at`. |
| terms | text | |

### RegisterReferralData

| Field | Type | Rules |
|---|---|---|
| referral_code | string | Valid code for an active program. |
| referee_email | string | Not the referrer's own — "Self-referrals are not allowed."; unique per program. |

## Output DTOs

### ReferralData

Referral projection — program, referrer contact, referee (email / contact), code, status, `converted_at`, `rewarded_at`.

## Public / Portal Endpoints

The referral-capture entry surface is **under-specified** in the source spec. An assumed public capture route accepts a `referral_code` and referee email from a form / landing page and calls `ReferralService::register(RegisterReferralData)` on the guest guard *(assumed)*. This route is currently absent from the spec and must be defined before build — see [[../unknowns]].

See [[../../../architecture/patterns/dto-pattern]].
