---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 7
status: complete
migration_range: 575000–579999
last_updated: 2026-05-12
---

# E-Signature Native

Multi-party electronic signing built into FlowFlex — eIDAS and ESIGN Act compliant, with full audit trail, signing order, and SMS/email identity verification. Eliminates DocuSign and HelloSign subscriptions.

**Panel:** `legal`  
**Phase:** 7  
**Migration range:** `575000–579999`

---

## Features

### Core (MVP)

- Document upload: PDF or generated from template → prepare for signing
- Signature fields: place signature, initials, date, text, checkbox fields on document
- Multi-party signing: add multiple signers with defined signing order (sequential or parallel)
- Email invitation: signers receive email with secure magic link — no account required
- Signer identity: email verification + optional SMS OTP
- Audit trail: every action logged (opened, viewed, signed) with timestamp + IP
- Signed document: merged PDF with embedded signatures + tamper-evident certificate
- Expiry: documents expire if not signed within configurable window

### Advanced

- SMS OTP verification: stronger identity check for high-value contracts
- In-person signing: device-based signing for face-to-face scenarios
- Signing reminders: configurable reminder schedule for pending signers
- Bulk sending: send same template to 100+ signers with unique data (e.g. employment contracts)
- Decline to sign: signer can decline with reason — triggers notification
- Embedded signing: iFrame embed for signing within customer portal

### AI-Powered

- Contract pre-fill: AI extracts key fields from contract text → pre-populate signer data fields
- Completion prediction: flag documents unlikely to be signed based on signer engagement

---

## Data Model

```erDiagram
    signature_documents {
        ulid id PK
        ulid company_id FK
        string title
        string original_file_url
        string signed_file_url
        string status
        string signing_order
        timestamp expires_at
        timestamp completed_at
    }

    signature_requests {
        ulid id PK
        ulid document_id FK
        string signer_name
        string signer_email
        string signer_phone
        integer signing_order
        string status
        string token
        json fields_data
        string ip_address
        timestamp signed_at
    }

    signature_audit_events {
        ulid id PK
        ulid document_id FK
        ulid request_id FK
        string event_type
        string ip_address
        string user_agent
        timestamp occurred_at
    }

    signature_documents ||--o{ signature_requests : "has"
    signature_documents ||--o{ signature_audit_events : "has"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `DocumentSigned` | All parties have signed | Contract Management (mark executed), Notifications |
| `DocumentDeclined` | Signer declines | Notifications (sender), Contract Management |
| `SigningReminderDue` | Scheduled reminder | Notifications (pending signer) |
| `DocumentExpired` | Signing window elapsed | Notifications (sender), Contract Management (reopen) |

---

## Permissions

```
legal.esign.send
legal.esign.view-own
legal.esign.view-any
legal.esign.void
legal.esign.download-audit-trail
```

---

## Competitors Displaced

DocuSign · HelloSign · Adobe Sign · PandaDoc · SignNow

---

## Related

- [[MOC_Legal]]
- [[contract-management]] — contracts signed via this module
- [[MOC_HR]] — employment contracts, policy sign-offs
- [[MOC_Projects]] — project approvals and change orders
