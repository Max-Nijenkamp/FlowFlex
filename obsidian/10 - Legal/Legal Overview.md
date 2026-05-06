---
tags: [flowflex, domain/legal, overview, phase/5]
domain: Legal & Compliance
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-06
---

# Legal Overview

Contract lifecycle, policy management, risk register, data privacy, and compliance tracking.

**Filament Panel:** `legal`
**Domain Colour:** Red `#DC2626` / Light: `#FEE2E2`
**Domain Icon:** `scale` (Heroicons)
**Phase:** 5

## Modules in This Domain

| Module | Description |
|---|---|
| [[Contract Management]] | Full CLM, e-signature, auto-renewal alerts |
| [[Policy Management]] | Policy publishing, employee acknowledgement |
| [[Risk Register]] | Risk identification, scoring, mitigation tracking |
| [[Data Privacy]] | GDPR/CCPA DSRs, right to erasure, consent |
| [[Insurance & Licence Tracking]] | Business insurance and regulatory licence register |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `ContractExpiring` | [[Contract Management]] | CRM (creates renewal task), Notifications |
| `RiskFlagRaised` | [[Risk Register]] | Legal, Notifications (notifies risk owner) |

## Related

- [[Contract Management]]
- [[Policy Management]]
- [[Data Privacy]]
- [[Panel Map]]
