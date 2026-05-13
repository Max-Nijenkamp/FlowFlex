---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.e-signatures
status: planned
color: "#4ADE80"
---

# E-Signatures

> eIDAS and ESIGN Act compliant multi-party electronic signing with full audit trail, signing order, and SMS identity verification — eliminating DocuSign and HelloSign subscriptions.

**Panel:** `legal`
**Module key:** `legal.e-signatures`

## What It Does

E-Signatures provides native electronic signing inside FlowFlex so companies do not need a separate DocuSign or HelloSign subscription. A sender uploads or generates a document, places signature and data fields on it, assigns signatories in the required signing order, and sends them a secure email link. Signatories sign in a browser without needing a FlowFlex account. Every action is logged in a tamper-evident audit trail. When all parties have signed, a merged PDF with embedded signatures and a certificate of completion is stored and linked to the originating contract record.

## Features

### Core
- Document preparation: upload a PDF or generate from a template; place signature, initials, date, text, and checkbox fields using a drag-and-drop field placer
- Signatory management: add multiple signatories with name, email, and signing order (sequential or parallel)
- Email invitation: each signatory receives a branded email with a unique secure magic link; no FlowFlex account required
- Email verification: link is single-use and tied to the recipient email address
- Signing interface: clean, mobile-friendly signing page; type, draw, or upload signature image
- Signed document: merged PDF with embedded signatures; certificate of completion attached with full audit log
- Expiry: document expires if not completed within a configurable window (default 30 days)

### Advanced
- SMS OTP verification: optional second factor — signatory receives OTP by SMS before signing; recorded in audit trail
- Signing order enforcement: sequential signing — signatory 2 is only notified when signatory 1 has signed
- Bulk sending: send the same template to 100+ signatories (e.g., employment contracts); each receives a pre-filled unique copy
- Decline to sign: signatory can decline with a reason; sender receives notification immediately
- Reminder automation: configurable reminder schedule for pending signatories (day 3, day 7, day 14)
- Void document: sender can void a document before all signatures are collected; audit trail updated
- Embedded signing: iFrame embed for signing within a customer portal or external web page

### AI-Powered
- Contract pre-fill: extract key data fields from the contract text and pre-populate signatory data fields to reduce manual entry
- Completion prediction: flag documents with low signatory engagement likely to expire without completion

## Data Model

```erDiagram
    legal_sign_documents {
        ulid id PK
        ulid company_id FK
        ulid contract_id FK
        string title
        string original_file_url
        string signed_file_url
        string status
        string signing_order_mode
        timestamp expires_at
        timestamp completed_at
        ulid sent_by FK
        timestamps timestamps
    }

    legal_sign_requests {
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

    legal_sign_audit_events {
        ulid id PK
        ulid document_id FK
        ulid request_id FK
        string event_type
        string ip_address
        string user_agent
        timestamp occurred_at
    }

    legal_sign_documents ||--o{ legal_sign_requests : "has"
    legal_sign_documents ||--o{ legal_sign_audit_events : "logged in"
```

| Table | Purpose |
|---|---|
| `legal_sign_documents` | Signing document with status and file URLs |
| `legal_sign_requests` | Per-signatory request with token and signing data |
| `legal_sign_audit_events` | Tamper-evident event log |

## Permissions

```
legal.e-signatures.send
legal.e-signatures.view-own
legal.e-signatures.view-any
legal.e-signatures.void
legal.e-signatures.download-audit-trail
```

## Filament

**Resource class:** `SignatureDocumentResource`
**Pages:** List, Create, View
**Custom pages:** `DocumentPreparationPage` (field placement interface), `SigningStatusPage` (per-signer status with timeline)
**Widgets:** `PendingSignaturesWidget` (documents awaiting action)
**Nav group:** Contracts

## Displaces

| Competitor | Feature Replaced |
|---|---|
| DocuSign | Multi-party electronic signing |
| HelloSign / Dropbox Sign | Document signing with audit trail |
| Adobe Sign | eSignature with identity verification |
| PandaDoc | Document signing and completion tracking |

## Implementation Notes

**External dependency — decision required before build:** The spec says "native electronic signing inside FlowFlex" — this implies building the signing infrastructure from scratch rather than using DocuSign/HelloSign APIs. Building native e-signatures requires:

1. **PDF field placement interface (`DocumentPreparationPage`):** A custom Filament `Page` that renders a PDF in-browser and allows drag-and-drop field placement. This requires a JavaScript PDF rendering library (**PDF.js** by Mozilla, MIT) to display the PDF canvas, and a custom canvas overlay for draggable field boxes (Alpine.js + position tracking). Field positions (page number, x, y, width, height) are stored in `legal_sign_requests.fields_data` JSON.

2. **PDF merging (signed PDF generation):** After all parties sign, the system must embed signature images and typed text into the PDF at the stored positions. Use `setasign/fpdi` + `setasign/fpdf` PHP packages to overlay field values onto the PDF. This is the most technically complex step — add these packages to `composer.json`.

3. **Certificate of completion:** Append a final page to the merged PDF listing all signers, timestamps, IP addresses, and user agents from `legal_sign_audit_events`. Rendered as a Blade view converted to PDF via dompdf and appended to the signed document.

4. **Tamper-evident audit trail:** Hash each audit event entry and chain the hashes (each event stores the hash of the previous event). The final hash is embedded in the signed PDF metadata. This is the eIDAS compliance requirement.

5. **SMS OTP:** Use a third-party SMS provider for OTP delivery. Add **Vonage (Nexmo) SMS API** or **Twilio** to `config/services.php`. Store credentials as `SMS_PROVIDER`, `SMS_API_KEY`, `SMS_API_SECRET`. The OTP is a 6-digit code stored (hashed) in `legal_sign_requests` with a 10-minute expiry.

**Signing interface:** The public signing page (`/sign/{token}`) is NOT a Filament page — it is a Vue 3 + Inertia page (or a standalone Blade route with no auth middleware) accessible without a FlowFlex account. The page renders the PDF (PDF.js), overlays the field inputs for the current signer, and on "Sign" calls a public API endpoint to record the signature.

**Signature drawing:** Use `signature_pad.js` (Szimek, MIT) for the draw-your-signature input. Captured as a base64 PNG, stored in S3, and rendered onto the PDF at the field position.

**Magic link security:** `legal_sign_requests.token` is a 64-character random hex string (`Str::random(64)`). It is single-use — mark as `used` after the signer submits. Rate-limit the signing endpoint to prevent brute-force token guessing.

**AI features:** Contract pre-fill uses `app/Services/AI/DocumentIntelligenceService.php` (shared with the document-intelligence module) to extract named fields from the contract text and pre-populate the signatory data fields. Completion prediction is a PHP rule: if `last_viewed_at` on any `legal_sign_requests` record is more than 5 days ago and status is still `pending`, flag as at-risk.

## Related

- [[contracts]] — contracts executed via this module; signed PDF stored on contract record
- [[document-review]] — documents reviewed before being sent for signing
- [[matter-management]] — settlement agreements and court submissions signed here
- [[../hr/INDEX]] — employment contracts signed via e-signatures
