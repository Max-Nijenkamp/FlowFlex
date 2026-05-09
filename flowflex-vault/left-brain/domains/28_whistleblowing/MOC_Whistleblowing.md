---
type: moc
domain: Whistleblowing & Ethics Hotline
panel: whistleblowing
cssclasses: domain-whistleblowing
phase: 4
color: "#6D28D9"
last_updated: 2026-05-09
---

# Whistleblowing & Ethics Hotline — Map of Content

Anonymous reporting channel for misconduct, fraud, harassment, and compliance violations. EU Whistleblower Directive (2019/1937) compliance for 50+ employee companies. Full case lifecycle management with reporter anonymity protection.

**Panel:** `whistleblowing`  
**Phase:** 4  
**Migration Range:** `1000000–1049999`  
**Colour:** Violet-700 `#6D28D9` / Light: `#EDE9FE`  
**Icon:** `heroicon-o-shield-exclamation`

---

## Why This Domain Exists

The EU Whistleblower Directive (2019/1937) became mandatory in December 2023 for companies with 50+ employees. Non-compliance: fines up to €30,000 per violation (varies by member state). Current alternatives:
- NAVEX (EthicsPoint): €15k+/year
- Speakfully: €12k+/year
- WhistleB: €10k+/year
- Most SMBs use email (not compliant — no anonymity guarantee)

FlowFlex Whistleblowing embeds compliance directly in the platform. No separate tool, no separate login, no separate vendor.

---

## Regulatory Scope

| Regulation | Scope | Effective |
|---|---|---|
| EU Whistleblower Directive 2019/1937 | EU companies 50+ employees | Dec 2023 |
| Sapin II (France) | French companies 50+ employees | 2017 |
| HinSchG (Germany) | German companies 50+ employees | Jul 2023 |
| UK Public Interest Disclosure Act | UK companies | 1998 |
| SEC Whistleblower Program | US public companies + subsidiaries | 2010 |

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[anonymous-intake-portal\|Anonymous Intake Portal]] | 4 | planned | Public reporting form, anonymous identity preservation, two-way messaging |
| [[case-management-investigation\|Case Management & Investigation]] | 4 | planned | Case lifecycle, investigator assignment, evidence, status tracking |
| [[eu-whistleblower-directive-compliance\|EU Directive Compliance]] | 4 | planned | 7-day acknowledgement, 3-month response SLA, data retention policy |
| [[reporter-communication-portal\|Reporter Communication Portal]] | 4 | planned | Encrypted two-way channel to anonymous reporter without revealing identity |
| Escalation & Legal Referral | 5 | planned | Escalation to external regulator, legal counsel handoff |
| Ethics Policy Management | 5 | planned | Code of conduct, policy distribution, signed acknowledgements |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `ReportSubmitted` | Intake Portal | Notifications (case manager), Case Management (create case) |
| `CaseAcknowledged` | Case Management | Reporter Portal (notify reporter), Compliance log |
| `CaseResolved` | Case Management | Reporter Portal (notify reporter), Analytics |
| `SLABreached` | Case Management | Notifications (compliance officer, legal) |
| `EscalationTriggered` | Case Management | Legal (create matter), Notifications |

---

## Anonymity Architecture

Reports are stored with no link to IP address or device fingerprint.  
Reporter assigned a `report_token` (UUID) only shown once at submission.  
All investigator-reporter messages are encrypted at rest with report-scoped keys.  
Company admins CANNOT see who submitted a report unless reporter self-identifies.

---

## Permissions Prefix

`ethics.reports.*` · `ethics.cases.*` · `ethics.policies.*`  
`ethics.compliance.*`

---

## Competitors Displaced

NAVEX EthicsPoint · Speakfully · WhistleB · Convercent · Vault Platform · CaseTrakker

---

## Related

- [[MOC_Domains]]
- [[MOC_Legal]] — legal matters raised from cases
- [[MOC_HR]] — HR investigations, disciplinary procedures
- [[MOC_IT]] — security incidents may originate from ethics reports
- [[auth-rbac]] — strict role isolation: case manager ≠ reporter viewer
