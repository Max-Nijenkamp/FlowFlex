---
type: module
domain: Document Management
panel: dms
phase: 4
status: planned
cssclasses: domain-dms
migration_range: 996000–996499
last_updated: 2026-05-09
---

# E-Signature

Legally binding electronic signatures for contracts, agreements, and HR documents. eIDAS compliant (EU). Supports SES, AdES, and QES tiers. Eliminates print-sign-scan workflows.

---

## Signature Tiers (eIDAS)

| Level | Use Case | Verification |
|---|---|---|
| **SES** (Simple) | Internal docs, low-risk | Tick box + email confirmation |
| **AdES** (Advanced) | Employment contracts, NDAs | Email + phone OTP |
| **QES** (Qualified) | High-value contracts | eIDAS-certified provider (iDIN / itsme / qualified certificate) |

Tier set per document template or per workflow step.

---

## Signing Flow

**Sender:**
1. Upload/generate document
2. Place signature fields (drag fields onto PDF)
3. Add signers (name + email + signing order)
4. Send for signature

**Signer:**
1. Receives email with secure link (no account required)
2. Reviews document
3. Draws, types, or uploads signature image
4. Completes verification step (OTP for AdES)
5. Clicks sign → confirmation email + signed PDF copy

---

## Signature Fields

Field types:
- Signature (handwritten or typed)
- Initials
- Date (auto-stamped)
- Text input (e.g., print name)
- Checkbox (agree to terms)

Placed on specific pages/coordinates in the document.

---

## Signed Document

After all signers complete:
- PDF cryptographically sealed (SHA-256 hash embedded)
- Audit certificate appended as final page (IP, timestamp, verification method per signer)
- Original document tamper-evident
- Stored in [[contract-repository]]

---

## Bulk Signing

HR use case: send same document (policy update) to 200 employees for acknowledgement:
- Single send → personalised per recipient
- Dashboard: % signed, who is outstanding
- Chase reminder in one click

---

## Decline / Reassign

Signer can decline (with reason) → workflow routed back to sender. Signer can reassign to another person (e.g., on leave).

---

## Data Model

### `dms_signature_envelopes`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| document_id | ulid | FK |
| status | enum | draft/sent/partial/completed/declined/expired |
| sent_at | timestamp | nullable |
| completed_at | timestamp | nullable |
| tier | enum | ses/ades/qes |

### `dms_signature_requests`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| envelope_id | ulid | FK |
| signer_name | varchar(200) | |
| signer_email | varchar(300) | |
| order | int | |
| status | enum | pending/signed/declined |
| signed_at | timestamp | nullable |
| ip_address | varchar(45) | nullable |
| verification_ref | varchar(200) | nullable |

---

## Migration

```
996000_create_dms_signature_envelopes_table
996001_create_dms_signature_requests_table
996002_create_dms_signature_fields_table
```

---

## Related

- [[MOC_DMS]]
- [[document-templates]]
- [[document-workflows]]
- [[contract-repository]]
