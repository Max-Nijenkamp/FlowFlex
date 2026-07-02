---
domain: crm
module: referral-program
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Referral Program — Decisions

## ADR: Manual reward fulfilment for v1

**Status**: decided (from spec). On a qualifying conversion, v1 creates a manual fulfilment task and notifies the fulfilment owner. Automated fulfilment via [[../../ecommerce/promotions/_module|Promotions]] / Finance is later *(assumed)*.

**Consequences**: no automated payout / discount-code issuance in v1; a human confirms and issues the reward, then marks the referral rewarded.

## ADR: Single reward per referral

**Status**: decided (from spec). Each referral yields at most one reward event (`rewarded_at` stamped once).

**Consequences**: simple leaderboard and reward accounting; multi-tier / recurring rewards are out of scope for v1.

## ADR: Fraud guards on registration

**Status**: decided (from spec). `register` rejects self-referrals (email or contact match with referrer) and duplicate referees per program via a unique constraint.

**Consequences**: basic abuse prevention at the point of capture; more advanced fraud scoring is not in scope for v1.
