---
domain: crm
module: referral-program
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Referral Program — Unknowns & Open Questions

## Assumptions

- The qualifying conversion is a referee deal won *(assumed)*.
- Reward fulfilment is manual in v1 *(assumed)*; automated fulfilment via promotions / finance is later.
- The leaderboard counts qualified + rewarded referrals only *(assumed)*.
- A public capture route exists on the guest guard behind a rate limiter *(assumed)* — see below.

## Open Questions

- **MISSING: public capture route spec.** The referral-capture entry surface (how a `referral_code` + referee email reach `ReferralService::register`) is under-specified in the source. Its route, guard, rate limiter, and company-context resolution must be defined before build.
- What exactly counts as a "qualifying conversion" — any won deal, or a deal above a value threshold?
- Can a program define multiple qualifying-conversion triggers (signup vs first purchase vs deal won)?
- How are rewards accounted when both referrer and referee rewards are configured?
