---
domain: crm
module: referral-program
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Referral Program — Security

## Permissions

| Permission | Purpose |
|---|---|
| crm.referrals.view-any | List / view referrals and programs. |
| crm.referrals.manage-programs | Create / edit programs and reward config. |
| crm.referrals.qualify | Mark a referral qualified. |
| crm.referrals.reward | Mark a referral rewarded. |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.referrals.view-any')
        && hasModule('crm.referrals');
}
```

## Tenant Isolation

`crm_referral_programs` and `crm_referrals` carry `company_id` and are scoped via `BelongsToCompany` / `CompanyScope`. The public capture route must resolve company context from the referral code, never from an app guard. See [[../../../security/tenancy-isolation]].

## Module Gating

Gated behind `crm.referrals` in [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.

## Source Security Notes

- **Public / portal guard (HIGH)** — the referral-capture route is an unauthenticated entry surface. It must run on the guest guard behind a named rate limiter. This surface is currently absent from the spec and must be documented explicitly before build. See [[../../../security/authn-authz]].
- **Fraud / self-referral** — `register` rejects self-referrals (referee email or contact matching the referrer) and enforces a duplicate guard (`unique(program_id, referee_email)`). See [[../../../security/threat-model]].
