---
type: gap
severity: high
category: data-model
status: resolved
resolved: 2026-06-11
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: vault-wide
---

# Sensitive PII/secrets not declared in encrypted-fields

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
External-person PII and provider secrets stored plaintext while `encrypted-fields` frontmatter was empty: extracted invoice IBANs/CV data, event attendee emails, workplace visitor emails, WhatsApp/SMS webhook_secret, offer salary, DSAR action notes.

## Impact
HIGH across AI, communications, events, workplace, legal, HR. Regulated PII at rest unencrypted.

## Proposed Solution
7 named specs FIXED this session (ai.document-intelligence, comms.whatsapp, comms.sms, events.registrations, hr.recruitment, legal.dsar, workplace.visitors) — encrypted-fields populated, 🔐 markers added, queryable email backed by *_hash column. Template convention now enforces the rule going forward (SEC-ENCRYPT in [[build/security-audit-2026-06-11]]). Re-scan other domains for any further sensitive columns.

## Resolution

All 7 flagged specs fixed: encrypted-fields populated, 🔐 markers added, encrypted columns set to text, events.registrations given attendee_email_hash for unique lookup, hr_offers.salary_cents→salary_raw.

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
