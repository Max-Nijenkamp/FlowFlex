---
type: moc
domain: IT & Security
panel: it
cssclasses: domain-it
phase: 4
color: "#6B7280"
last_updated: 2026-05-08
---

# IT & Security — Map of Content

IT asset management, internal helpdesk, SaaS spend, access auditing, security & compliance, uptime monitoring, SSO/IdP, and MDM.

**Panel:** `it`  
**Phase:** 4–6  
**Migration Range:** `500000–549999`  
**Colour:** Gray-500 `#6B7280` / Light: `#F9FAFB`  
**Icon:** `heroicon-o-shield-check`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| IT Asset Management | 4 | planned | Hardware register, assignments, lifecycle, warranty |
| [[itsm-helpdesk\|ITSM Helpdesk]] | 4 | planned | ITIL-aligned ticketing, SLAs, automation, KB deflection |
| [[service-catalog-it\|IT Service Catalogue]] | 4 | planned | Requestable services, onboarding bundles, approval flows |
| [[change-management-itil\|Change Management (ITIL)]] | 4 | planned | CAB reviews, change calendar, blackout periods, backout plans |
| SaaS Spend Management | 4 | planned | SaaS inventory, spend tracking, renewal alerts |
| Access & Permissions Audit | 4 | planned | Cross-app permission snapshots, access reviews |
| Security & Compliance | 4 | planned | SOC 2, ISO 27001 evidence, policy sign-off |
| Uptime & Status Monitoring | 6 | planned | Service health, incident pages, alerting |
| SSO & Identity Provider | 6 | planned | SAML 2.0, OIDC, SCIM, WebAuthn/FIDO2 |
| MDM & Device Management | 6 | planned | macOS/iOS/Android/Windows, remote wipe, BYOD |
| [[team-password-secrets-vault\|Team Password & Secrets Vault]] | 4 | planned | AES-256 encrypted vault, secret rotation, breach alerts |
| [[it-procurement-requests\|IT Procurement & Hardware Requests]] | 4 | planned | Self-service catalog, approval workflow, auto-link to IT Asset |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `EmployeeHired` | HR (consumed) | IT (provision accounts, assign assets) |
| `EmployeeOffboarded` | HR (consumed) | IT (revoke all access, reclaim assets) |
| `SaaSRenewalDue` | SaaS Spend | Notifications (IT manager) |
| `SecurityIncidentRaised` | Security | Notifications (all managers), Legal |
| `DeviceNonCompliant` | MDM | Notifications, IT Helpdesk (auto-ticket) |

---

## Permissions Prefix

`it.assets.*` · `it.helpdesk.*` · `it.saas.*` · `it.access-audit.*`  
`it.security.*` · `it.monitoring.*` · `it.sso.*` · `it.mdm.*`

---

## Competitors Displaced

Freshservice · Jira Service Management · Okta · Jamf · BetterCloud · Snipe-IT

---

## Related

- [[MOC_Domains]]
- [[MOC_HR]] — employee lifecycle events
- [[MOC_Legal]] — security & compliance evidence → legal
- [[auth-rbac]] — SSO integration with platform auth
