---
domain: crm
module: referral-program
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Referral Program — Architecture

## State Machine

No formal spatie state machine. A referral moves through a simple status list on `crm_referrals.status`:

```
pending → qualified → rewarded
        ↘ rejected
```

- `pending` — referee registered, not yet converted.
- `qualified` — qualifying conversion detected (manual or referee deal won *(assumed)*).
- `rewarded` — reward fulfilled and stamped.
- `rejected` — failed a fraud check or program terms.

## Services & Actions

| Method | Signature | Purpose |
|---|---|---|
| `ReferralService::codeFor` | `(contactId, programId): string` | Generate-or-return the referrer's code. |
| `ReferralService::register` | `(RegisterReferralData): ReferralData` | Fraud checks: self-referral by email/contact match, duplicate guard. |
| `ReferralService::qualify` | `(referralId): ReferralData` | On conversion (manual or deal-won check); notifies fulfilment owner. |
| `ReferralService::markRewarded` | `(referralId): ReferralData` | Stamps `rewarded_at`. |
| `ReferralService::leaderboard` | `(programId): Collection` | Top referrers. |

## Events

None fired or consumed.

## Filament Artifacts

Nav group: **Intelligence**.

| # | Artifact | ui-strategy row | Notes |
|---|---|---|---|
| 1 | `ReferralProgramResource` | CRUD resource | Reward config. |
| 1 | `ReferralResource` | CRUD resource | Status pipeline, qualify / reward actions. |
| 9 | `ReferralLeaderboardPage` | Report custom page | Top referrers. |

**Access contract**: `canAccess()` = `can('crm.referrals.view-any') && hasModule('crm.referrals')`. See [[../../../architecture/filament-patterns]].

## Jobs & Scheduling

None specified. Qualification is triggered manually or on a referee deal-won check.

## Caching

None.

## Search & Realtime

None.
