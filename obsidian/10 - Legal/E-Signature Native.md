---
tags: [flowflex, domain/legal, e-signature, phase/4]
domain: Legal
panel: legal
color: "#7C3AED"
status: planned
last_updated: 2026-05-08
---

# E-Signature Native

Sign contracts inside FlowFlex without a DocuSign subscription. Legally binding e-signatures for EU (eIDAS), UK, and US (ESIGN Act). Send, sign, track, and store — all in one flow. Works with contracts, HR documents, proposals, and custom documents.

**Who uses it:** Legal, HR (employment contracts), Sales (proposals), Procurement
**Filament Panel:** `legal`; Vue + Inertia (signer experience)
**Depends on:** Core, [[Contract Management]], [[Document Management]], [[AI Contract Intelligence]]
**Phase:** 4

---

## Features

### Document Preparation

- Upload PDF or Word document
- Drag-and-drop signature fields: signature, initials, date, text input, checkbox
- Multi-party: place fields per signer (colour-coded per recipient)
- Signing order: sequential (A signs, then B) or parallel (all sign simultaneously)
- Required vs optional fields
- Page thumbnail preview while placing fields

### Sending for Signature

- Add signers: name + email (internal tenant or external email)
- Personalised message to each signer
- Deadline: optional signing deadline with reminder automation
- Send method: email link (no account required), or send to FlowFlex user inbox
- CC recipients: stakeholders who receive a copy but don't sign

### Signer Experience

- No account required for external signers
- Email link → opens secure signing page in browser
- Identity verification options:
  - Email OTP (basic — eIDAS simple)
  - SMS OTP (advanced)
  - ID verification via Onfido/Stripe Identity (qualified, optional)
- Guided signing: "Click here to sign" walkthrough
- Draw, type, or upload signature image
- Initials for initials fields
- Complete: all parties notified, all receive signed PDF copy

### Audit Trail & Legal Validity

- Audit trail per document: who viewed, who signed, IP address, timestamp, device
- Certificate of completion: PDF with full audit log appended to signed document
- eIDAS compliance: simple electronic signature (SES) by default; advanced (AES) with SMS OTP
- ESIGN Act (US): enforceable for US parties
- Tamper-evident: signed PDF digitally sealed with SHA-256 hash
- Long-term archiving: signed documents stored immutably in S3

### Template Library

- Create signing templates from common documents: NDA, employment contract, service agreement
- Predefined field placements — send a new document in 30 seconds
- Variable substitution: {{party_name}}, {{date}}, {{amount}} auto-filled from record
- Link to CRM Deal or HR Employee for context

### Automation Triggers

- Auto-send for signature: employment contract sent on offer accepted in HR
- Auto-send: NDA when new lead qualifies in CRM (rule-based trigger)
- Workflow integration: Automation Builder can trigger document send on any event
- On completion: auto-attach signed PDF to: CRM deal, HR employee profile, Finance invoice

### Tracking & Reminders

- Real-time status: not opened / viewed / signed / declined per recipient
- Automatic reminders: 3 days, 1 day before deadline
- Nudge manually: resend reminder with one click
- Void document: cancel signing request, notify all parties
- Decline: signer can decline with reason → notified

---

## Database Tables (3)

### `legal_signature_requests`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `document_file_id` | ulid FK | original |
| `signed_file_id` | ulid FK nullable | completed doc |
| `audit_trail_file_id` | ulid FK nullable | cert of completion |
| `signing_order` | enum | `sequential`, `parallel` |
| `status` | enum | `draft`, `sent`, `partial`, `completed`, `voided`, `declined` |
| `deadline_at` | timestamp nullable | |
| `created_by` | ulid FK | |
| `completed_at` | timestamp nullable | |

### `legal_signature_recipients`
| Column | Type | Notes |
|---|---|---|
| `request_id` | ulid FK | |
| `name` | string | |
| `email` | string | |
| `role` | enum | `signer`, `cc` |
| `order_index` | integer | for sequential |
| `token` | string unique | secure signing link |
| `status` | enum | `pending`, `viewed`, `signed`, `declined` |
| `signed_at` | timestamp nullable | |
| `ip_address` | string nullable | |
| `verification_method` | enum nullable | `email_otp`, `sms_otp`, `id_verify` |

### `legal_signature_fields`
| Column | Type | Notes |
|---|---|---|
| `request_id` | ulid FK | |
| `recipient_id` | ulid FK | |
| `field_type` | enum | `signature`, `initials`, `date`, `text`, `checkbox` |
| `page` | integer | |
| `x` | decimal | position % |
| `y` | decimal | position % |
| `width` | decimal | |
| `height` | decimal | |
| `required` | boolean | |
| `value` | text nullable | filled value |

---

## Permissions

```
legal.e-signature.create
legal.e-signature.send
legal.e-signature.view
legal.e-signature.void
legal.e-signature.view-audit-trail
legal.e-signature.manage-templates
```

---

## Competitor Comparison

| Feature | FlowFlex | DocuSign | HelloSign | Adobe Sign |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€10+/mo) | ❌ (€15+/mo) | ❌ (€15+/mo) |
| eIDAS compliance | ✅ | ✅ | ✅ | ✅ |
| Auto-send on HR/CRM trigger | ✅ | ❌ | ❌ | ❌ |
| Integrated audit trail in platform | ✅ | ✅ | ✅ | ✅ |
| AI contract intelligence link | ✅ | ❌ | ❌ | ❌ |
| B2B NL/EU NDA templates | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[Legal Overview]]
- [[Contract Management]]
- [[AI Contract Intelligence]]
- [[Document Management]]
- [[Workflow Automation Builder]]
