---
type: gap
severity: high
category: security
status: resolved
resolved: 2026-06-11
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: vault-wide
---

# Inbound webhook signature verification stated as assumption, not requirement

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
Inbound webhook surfaces that flip deliverability flags or confirm payments rely on `*(assumed)*` verification with no named middleware or secret source.

## Impact
HIGH in foundation/email-setup (Resend bounce flips email_deliverable), events/tickets + ecommerce/payments (Stripe payment confirmation), crm/email-integration (OAuth state/PKCE). Spoofing and payment-confirmation vectors.

## Proposed Solution
Promote verification from assumption to a stated requirement naming the mechanism + secret source. Template now forbids `*(assumed)*` on webhook verification. Per-spec list in [[build/security-audit-2026-06-11]] (SEC-WEBHOOK).

## Resolution

**Security notes** (Webhook verification, HIGH) added to the 3 flagged specs (crm.email-integration, events.tickets, foundation.email-setup) promoting verification from *(assumed)* to a stated requirement naming mechanism + secret source.

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
