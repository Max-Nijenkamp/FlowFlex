---
tags: [flowflex, domain/it, overview, phase/5]
domain: IT & Security Management
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-06
---

# IT Overview

IT asset management, internal helpdesk, SaaS spend, access auditing, and security compliance.

**Filament Panel:** `it`
**Domain Colour:** Slate `#475569` / Light: `#F1F5F9`
**Domain Icon:** `shield-check` (Heroicons)
**Phase:** 5

## Modules in This Domain

| Module | Description |
|---|---|
| [[IT Asset Management]] | Hardware and software lifecycle, licence compliance |
| [[Internal IT Helpdesk]] | Employee IT support tickets, SLA tracking |
| [[SaaS Spend Management]] | SaaS discovery, spend tracking, shadow IT |
| [[Access & Permissions Audit]] | Cross-system access map, overprovision alerts |
| [[Security & Compliance]] | GDPR/ISO 27001/SOC 2 readiness tooling |
| [[Uptime & Status Monitoring]] | Service monitoring, public status page |

## Key Events Consumed

| Event | From | What IT Does |
|---|---|---|
| `EmployeeHired` | [[Recruitment & ATS]] | Provisions access per onboarding template |
| `OffboardingCompleted` | [[Offboarding]] | Revokes all system access |
| `SaaSLicenceExpiring` | [[SaaS Spend Management]] | Alerts finance, creates renewal task |

## Related

- [[IT Asset Management]]
- [[Internal IT Helpdesk]]
- [[Access & Permissions Audit]]
- [[Panel Map]]
