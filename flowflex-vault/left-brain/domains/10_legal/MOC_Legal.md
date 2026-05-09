---
type: moc
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 4
color: "#92400E"
last_updated: 2026-05-08
---

# Legal & Compliance — Map of Content

Contract lifecycle management, policy management, risk register, data privacy (GDPR), insurance, AI contract intelligence, and native e-signature.

**Panel:** `legal`  
**Phase:** 4–7  
**Migration Range:** `550000–599999`  
**Colour:** Amber-800 `#92400E` / Light: `#FFFBEB`  
**Icon:** `heroicon-o-scale`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Contract Management | 4 | planned | Contract lifecycle, metadata, expiry alerts, templates |
| Policy Management | 4 | planned | Policy library, version control, mandatory sign-off |
| Risk Register | 4 | planned | Risk identification, scoring, mitigation, reviews |
| Data Privacy | 4 | planned | GDPR consent, DSARs, data inventory, DPIAs |
| [[dsar-self-service-portal\|DSAR Self-Service Portal]] | 4 | planned | Public DSAR submission, identity verification, automated erasure |
| Insurance & Licence Tracking | 7 | planned | Policy register, certificates, renewal reminders |
| AI Contract Intelligence | 7 | planned | Clause extraction, risk scoring, obligation tracking |
| E-Signature Native | 7 | planned | Multi-party signing, audit trail, eIDAS compliant |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `ContractExpiring` | Contract Management | Notifications (legal team) |
| `PolicySignOffDue` | Policy Management | Notifications (employee), HR Compliance |
| `RiskEscalated` | Risk Register | Notifications (board-level) |
| `DSARReceived` | Data Privacy | Legal (start erasure flow), Notifications |
| `DocumentSigned` | E-Signature | Contract Management (mark executed), Projects |
| `SecurityIncidentRaised` | IT (consumed) | Legal (record potential liability) |

---

## Permissions Prefix

`legal.contracts.*` · `legal.policies.*` · `legal.risks.*`  
`legal.privacy.*` · `legal.insurance.*` · `legal.esign.*`

---

## Competitors Displaced

DocuSign · HelloSign · ContractSafe · Juro · OneTrust · Ironclad · Kira Systems

---

## Related

- [[MOC_Domains]]
- [[MOC_IT]] — security incidents → legal records
- [[MOC_HR]] — policy sign-off → HR compliance
- [[MOC_Finance]] — contracts → invoices
- [[MOC_Projects]] — document approvals + e-sign
