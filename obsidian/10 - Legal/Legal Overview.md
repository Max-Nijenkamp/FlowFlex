---
tags: [flowflex, domain/legal, overview, phase/7]
domain: Legal & Compliance
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-07
---

# Legal Overview

Contract lifecycle, policy management, risk register, data privacy, insurance/licence tracking, AI contract intelligence, and native e-signature. All 7 modules built in Phase 4–7.

**Filament Panel:** `legal`
**Domain Colour:** Red `#DC2626` / Light: `#FEE2E2`
**Domain Icon:** `heroicon-o-scale`
**Phase:** 4–7 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[Contract Management]] | Full CLM — contracts, parties, versions, e-signature, auto-renewal alerts |
| [[Policy Management]] | Policy publishing, versioning, employee acknowledgement tracking |
| [[Risk Register]] | Risk identification, likelihood/consequence scoring, mitigations, periodic reviews |
| [[Data Privacy]] | GDPR/CCPA DSRs, processing activities, consent records, DPIAs |
| [[Insurance & Licence Tracking]] | Business insurance and regulatory licence register with expiry reminders |
| [[AI Contract Intelligence]] | AI clause extraction, risk scoring, obligation tracking, template comparison |
| [[E-Signature Native]] | Native e-signatures (eIDAS/ESIGN), multi-party, audit trail, auto-send triggers |

## Filament Panel Structure

**Navigation Groups:**
- `Contracts` — Contracts, Contract Parties, Contract Versions
- `Policies` — Policies, Policy Acknowledgements
- `Risk` — Risks, Risk Mitigations, Risk Reviews
- `Privacy` — DSR Requests, Processing Activities, Consent Records, DPIAs
- `Licences` — Insurance Policies, Regulatory Licences

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `ContractExpiring` | Contract Management | CRM (creates renewal task), Notifications |
| `ContractSigned` | Contract Management | Notifications (all contract parties) |
| `RiskFlagRaised` | Risk Register | Legal team notifications, risk owner notification |
| `PolicyPublished` | Policy Management | Notifications (all relevant tenants) |
| `PolicyAcknowledgementOverdue` | Policy Management | Reminder notification to tenant + manager |
| `InsuranceExpiring` | Insurance & Licences | Legal team notifications |
| `LicenceExpiring` | Insurance & Licences | Legal team notifications |

## Permissions Prefix

`legal.contracts.*` · `legal.policies.*` · `legal.risks.*`  
`legal.privacy.*` · `legal.licences.*`

## Database Migration Range

`900000–949999`

## Related

- [[Contract Management]]
- [[Policy Management]]
- [[Risk Register]]
- [[Data Privacy]]
- [[Insurance & Licence Tracking]]
- [[Panel Map]]
- [[Build Order (Phases)]]
