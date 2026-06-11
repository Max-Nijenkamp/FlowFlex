---
type: gap
severity: high
category: architecture
status: open
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: vault-wide
---

# Public/external surfaces declare no guest/portal guard boundary

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
Public booking, quote-accept, deal-room, referral, NPS/CSAT, form-submit, careers-apply, chat-widget, cart-recovery and email-tracking endpoints describe a token but never declare the guest / scoped-portal guard isolating them from the app Sanctum session. Signed-URL and single-use token semantics are frequently unstated.

## Impact
Recurring HIGH across CRM, ecommerce, marketing, HR, support, LMS, customer-success. Payment-triggering and write-creating public surfaces are the most dangerous (session confusion, replay, unauthorised writes).

## Proposed Solution
Each public surface must declare its guest/scoped-portal guard + signed/single-use token semantics. Per-spec list in [[build/security-audit-2026-06-11]] (SEC-EXTERNAL). Prioritise crm.scheduling, events.tickets, marketing.forms, hr.recruitment apply, ecommerce cart-recovery/reviews.

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
