---
type: moc
domain: Document Management & E-Signatures
panel: dms
phase: 4
color: "#8B5CF6"
cssclasses: domain-dms
last_updated: 2026-05-09
---

# Document Management & E-Signatures — Map of Content

Centralised document repository with version control, approval workflows, legally binding e-signatures, contract repository, and document automation. Replaces DocuSign, PandaDoc, Ironclad, and SharePoint for mid-market companies.

**Panel:** `dms`  
**Phase:** 4  
**Migration Range:** `995000–999999`  
**Colour:** Violet `#8B5CF6` / Light: `#EDE9FE`  
**Icon:** `heroicon-o-document-text`

---

## Why This Domain Exists

Every company signs contracts, sends proposals, stores policies, and manages documents. Current costs:
- DocuSign: €30+/user/month (just for e-signatures)
- PandaDoc: €30+/user/month
- Adobe Sign: €25+/user/month
- Ironclad (CLM): €50k+/year

FlowFlex DMS covers the full document lifecycle. Legal module already stores some documents — DMS adds the process layer: templates, workflows, e-sign, version control.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Document Templates | 4 | planned | Merge-field templates for contracts, proposals, policies, NDAs |
| Document Workflows | 4 | planned | Multi-step approval routing before finalise/send |
| E-Signature | 4 | planned | Legally binding signatures (eIDAS EU, ESIGN US), audit trail |
| Contract Repository | 4 | planned | Signed contracts with search, expiry alerts, obligation tracking |
| Version Control | 4 | planned | Document revision history, compare versions, restore previous |
| Document Automation | 5 | planned | Auto-generate documents from CRM data, triggered by events |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `DocumentSentForSignature` | E-Signature | Notifications (signers), CRM (activity log) |
| `DocumentSigned` | E-Signature | CRM (deal closed), Legal (contract filed), Notifications |
| `ContractExpiringSoon` | Contract Repository | Legal (renewal action), Notifications (owner), CRM |
| `DocumentApproved` | Workflows | Notifications (requester), DMS (version locked) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Documents` — All Documents, My Documents, Shared, Archived
- `Templates` — Template Library, Merge Fields, Preview
- `Signatures` — Pending Signatures, Sent, Completed, Declined
- `Contracts` — Contract Register, Expiring, Obligations
- `Workflows` — Active Workflows, Approval Templates

---

## eIDAS Compliance (EU)

Three signature levels supported:
- **Simple Electronic Signature (SES)**: click-to-accept — valid for internal approvals, low-risk commercial
- **Advanced Electronic Signature (AdES)**: email OTP verification + audit trail — valid for most B2B contracts
- **Qualified Electronic Signature (QES)**: eID verification (DigiD/BankID/iDIN) — legally equivalent to handwritten in EU

---

## Permissions Prefix

`dms.documents.*` · `dms.templates.*` · `dms.signatures.*`  
`dms.contracts.*` · `dms.workflows.*`

---

## Competitors Displaced

DocuSign · PandaDoc · Adobe Sign · HelloSign (Dropbox Sign) · Ironclad · ContractPodAi · SharePoint (document mgmt)

---

## Related

- [[MOC_Domains]]
- [[MOC_Legal]] — contracts stored in Legal document vault
- [[MOC_CRM]] — contracts linked to CRM companies/deals
- [[MOC_HR]] — employment contracts, offer letters
- [[MOC_Finance]] — supplier contracts, customer agreements
