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

# File uploads omit whitelist + size + tenant path contract

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
The `companies/{id}/` storage path, MIME whitelist, and max size are repeatedly delegated to a generic 'core.files' / 'security rules' reference instead of being restated as an enforced contract in the spec.

## Impact
MEDIUM across nearly every domain handling attachments. Untyped/oversized uploads and tenant-path leakage risk.

## Proposed Solution
Each upload-bearing spec restates type-whitelist + max-size + companies/{id}/ path. Per-spec list in [[build/security-audit-2026-06-11]] (SEC-UPLOAD). Template convention now requires it.

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
