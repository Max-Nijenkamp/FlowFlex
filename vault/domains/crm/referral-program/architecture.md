---
domain: crm
module: referral-program
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Intelligence

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ReferralProgramResource` | #1 CRUD resource | tweaks: inline-relation-repeater (reward config) | reward structure, terms, active period |
| `ReferralResource` | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (qualify / reward / reject) | status pipeline `pending → qualified → rewarded` (+ `rejected`) |
| `ReferralLeaderboardPage` | #9 Report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] | top referrers (qualified + rewarded counts) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.referrals.view-any') && BillingService::hasModule('crm.referrals')`
per [[../../../architecture/filament-patterns]] #1. `ReferralLeaderboardPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. The public referral-capture route (unauthenticated
registration entry, [[features/referral-tracking]]) is Vue+Inertia per [[../../../architecture/ui-strategy]] with a
guest guard behind a named rate limiter, resolving company context from the referral code — not a Filament artifact,
and currently an under-specified gap ([[unknowns]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Program / referral CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Register referral (fraud + duplicate guard) | Pessimistic | `unique(program_id, referee_email)` DB constraint inside `DB::transaction()` so a concurrent double-submit yields one row |
| Qualify / reward / reject transition | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read status, single-stamp `rewarded_at` — reward mutates credit/payout (money) per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None specified. Qualification is triggered manually or on a referee deal-won check.

## Caching

None.

## Search & Realtime

None.
