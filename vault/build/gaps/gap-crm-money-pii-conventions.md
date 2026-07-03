---
type: gap
severity: medium
category: data-model
status: open
domain: crm
color: "#F97316"
discovered: 2026-07-03
discovered-in: crm.referral-program
---

# Gap — referral reward money in jsonb; lead phone/email conventions undecided

## Context

Wave 2 batch 2 propagation flagged two crm data-model convention issues (not fixed per no-silent-fix rule).

## Problem

1. referral-program stores reward `value` inside a jsonb `{type,value}` blob — cash/credit rewards are money and must be integer minor units via brick/money, not loose json numbers.
2. `crm_leads.phone` has no E.164 note and lead email/phone are plaintext (spec marks encryption *(assumed)* none) — needs an explicit decision either way.

## Impact

Reward payouts risk float/format drift outside the money convention; lead PII handling is unspecified for GDPR/data-lifecycle purposes.

## Proposed Solution

1. Split reward into `reward_type` + `reward_amount_cents` (bigint) or document a typed DTO wrapping the jsonb with integer-only cash values.
2. Add E.164 (propaganistas/laravel-phone) to the leads data model; record an explicit encryption decision for lead contact fields (likely plaintext + retention rules, matching contacts).
