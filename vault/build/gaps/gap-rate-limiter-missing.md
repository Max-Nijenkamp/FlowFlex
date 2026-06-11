---
type: gap
severity: medium
category: security
status: open
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: vault-wide
---

# No rate limiter on expensive/abuse-prone surfaces

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
Two sub-classes lack a cited rate limiter: (a) inbound webhooks (Stripe, SMS, WhatsApp, email, Resend) and (b) heavy exports / PDF / bulk-import / public token endpoints. Signature verification often present but edge throttling absent.

## Impact
MEDIUM, very widespread. Abuse / amplification / cost-runaway risk.

## Proposed Solution
Cite a rate limiter (throttle / RateLimiter::for) on the affected actions per [[architecture/security]]. Per-spec list in [[build/security-audit-2026-06-11]] (SEC-RATELIMIT). Template convention now requires it.

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
