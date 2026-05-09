---
tags: [flowflex, domain/it, overview, phase/6]
domain: IT & Security Management
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# IT Overview

IT asset lifecycle, internal helpdesk, SaaS spend discovery, access auditing, security compliance, uptime monitoring, SSO/identity provider, and MDM device management. All 8 modules built in Phase 4–6.

**Filament Panel:** `it`
**Domain Colour:** Slate `#475569` / Light: `#F1F5F9`
**Domain Icon:** `heroicon-o-shield-check`
**Phase:** 4–6 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[IT Asset Management]] | Hardware + software lifecycle, licence compliance, seat tracking |
| [[Internal IT Helpdesk]] | Employee IT support tickets, SLA policies, categories, internal notes |
| [[SaaS Spend Management]] | SaaS discovery, spend tracking, shadow IT flagging, renewal alerts |
| [[Access & Permissions Audit]] | Cross-system access snapshots, overprovision alerts |
| [[Security & Compliance]] | Compliance frameworks (GDPR/ISO27001/SOC2), controls, evidence tracking |
| [[Uptime & Status Monitoring]] | Service monitoring, status checks, incidents, public status page |
| [[SSO & Identity Provider]] | SAML 2.0 + OIDC SSO, SCIM provisioning, MFA, FlowFlex as IdP |
| [[MDM & Device Management]] | macOS/iOS/Android/Windows device enrolment, policies, remote wipe, BYOD |

## Filament Panel Structure

**Navigation Groups:**
- `Assets` — IT Assets, Software Licences, Licence Allocations
- `Helpdesk` — IT Tickets, SLA Policies
- `SaaS` — SaaS Subscriptions, Spend Records, Shadow IT Discoveries
- `Access` — Access Snapshots, Overprovision Alerts
- `Compliance` — Frameworks, Controls, Evidence, Assessments
- `Monitoring` — Monitored Services, Status Checks, Incidents, Status Pages

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `SoftwareLicenceExpiring` | IT Asset Management | Finance (create renewal task), Notifications |
| `ITTicketCreated` | IT Helpdesk | Notifications (notify assigned tech) |
| `ITTicketSLABreached` | IT Helpdesk | Notifications (escalate to IT manager) |
| `SaaSLicenceExpiring` | SaaS Spend | Finance + IT notifications |
| `OverprovisionAlertRaised` | Access Audit | IT security notifications |
| `ComplianceControlFailed` | Security | Notifications (notify compliance officer) |
| `ServiceDown` | Uptime Monitor | Immediate alert to IT team via all channels |
| `ServiceRecovered` | Uptime Monitor | Resolution notification to IT team |
| `EmployeeHired` | HR (Phase 2) | IT Helpdesk (create access provisioning ticket) |
| `OffboardingCompleted` | HR (Phase 2) | IT (revoke all system access, trigger access audit) |

## Permissions Prefix

`it.assets.*` · `it.helpdesk.*` · `it.saas.*`  
`it.access.*` · `it.compliance.*` · `it.monitoring.*`

## Database Migration Range

`850000–899999`

## Related

- [[IT Asset Management]]
- [[Internal IT Helpdesk]]
- [[SaaS Spend Management]]
- [[Access & Permissions Audit]]
- [[Security & Compliance]]
- [[Uptime & Status Monitoring]]
- [[Panel Map]]
- [[Build Order (Phases)]]
