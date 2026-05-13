---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 4
status: complete
migration_range: 500000–549999
last_updated: 2026-05-12
---

# DSAR Self-Service Portal

Customer-facing GDPR Data Subject Access Request portal. Individuals submit, verify identity, and track requests without contacting support. Backend automates data collection across all domains. Replaces manual email workflows, spreadsheet tracking, and point solutions like DataGrail and OneTrust DSAR.

**Panel:** `legal` (admin) + Vue+Inertia frontend (`/privacy/` routes, public)  
**Phase:** 4

---

## Why Phase 4

Phase 4 introduces Ecommerce (public customers) and Operations (contractors/suppliers). Both groups have GDPR rights. DSAR workflow must be operational before the company acquires non-employee data subjects at scale.

Legal obligation: 30-day response window from GDPR Article 12. Manual process breaks at >50 requests/month.

---

## Features

### Public Request Submission (`/privacy/requests`)
- Request type selection: Access / Erasure (Right to Be Forgotten) / Rectification / Portability / Restriction / Objection
- Identity fields: name, email, any account reference numbers
- Country selector (determines which regulation applies — GDPR, UK GDPR, CCPA, LGPD, PIPL)
- Consent checkbox + plain-language description of what will happen
- File upload for ID verification documents (passport, utility bill — if required by policy)
- CAPTCHA to prevent spam submission
- Confirmation email with request reference number and 30-day deadline

### Identity Verification
- Email link verification (low risk requests — Access, Portability)
- ID document upload + manual review (high risk — Erasure, Rectification)
- For authenticated users (customers with portal account): auto-verified via session
- Verification status per request: Pending / Verified / Rejected
- Rejection reason codes with appeal path

### Automated Data Collection
- On request verified → system auto-queries all domains for subject's data:

| Domain | Data Collected |
|---|---|
| CRM | Contact record, interactions, notes, tags |
| Finance | Invoices, payment history, bank details |
| HR | Only if data subject is also an employee |
| Ecommerce | Orders, cart history, wishlists, addresses |
| Marketing | Email subscriptions, campaign interactions, form submissions |
| Support | Tickets, chat transcripts |
| Analytics | Behavioural event logs (if linkable to identity) |
| Community | Profile, posts, comments |

- Data collection runs async job per domain (dispatches `DSARDataCollectionRequested` event)
- Each domain responds with a structured JSON payload
- Aggregator compiles into unified report

### Access Request Fulfilment
- Generate PDF report: all personal data held, source system, legal basis, retention period
- Machine-readable JSON export (GDPR Article 20 portability)
- Download link emailed to verified requester (secure expiring URL, 72h)
- Audit log: who accessed the generated report

### Erasure Request Processing
- Legal hold check: if data subject is party to active contract, invoice, or litigation hold — partial erasure only
- Erasure scope per domain:
  - CRM: anonymise contact (replace name/email with `REDACTED_[ulid]`)
  - Finance: anonymise payer name (retain transaction records for tax law — 7 years)
  - Marketing: hard delete from all lists, suppress from future import
  - Ecommerce: anonymise account, retain order records (VAT law)
  - Community: anonymise username, retain content (unless content-only erasure requested)
- Erasure completion triggers `DSAREraseCompleted` event → notifies all domain handlers
- Suppression list: email address added to do-not-contact suppress list to prevent re-import

### Request Tracking (Public)
- Requester enters reference number + email → see current status
- Status timeline: Submitted → Verified → Processing → Ready → Closed
- Estimated completion date shown (calculated from submission + 30 days)
- Ability to upload additional documents or add a message

### Admin Dashboard (Legal Panel)
- All open requests with SLA countdown (RAG: green >14d, amber 8–14d, red <7d)
- Manual verification queue (ID documents awaiting review)
- Override tools: manually advance status, add legal hold, override erasure scope
- Bulk actions: assign to handler, close batch
- Regulation filter (GDPR vs CCPA vs LGPD — different obligations)
- Monthly compliance report: requests by type, average response time, SLA breach count

### Regulation Engine
- Company's applicable regulations configured per country they operate in
- CCPA (California): 45-day window, opt-out of sale, different data categories
- LGPD (Brazil): similar to GDPR, Portuguese language support
- PIPL (China): restricted to Chinese residents, stricter deletion rules
- UK GDPR: post-Brexit, same 30-day window, ICO reporting endpoint
- Rule set determines: response deadline, which data types are in scope, whether ID verification is required

---

## Data Model

```erDiagram
    dsar_requests {
        ulid id PK
        ulid company_id FK
        string reference_number
        string request_type
        string regulation
        string status
        string subject_name
        string subject_email
        string subject_country
        boolean identity_verified
        json verification_documents
        timestamp submitted_at
        timestamp verified_at
        timestamp deadline_at
        timestamp completed_at
        ulid assigned_to FK
        json legal_hold_reasons
    }

    dsar_data_collections {
        ulid id PK
        ulid request_id FK
        string domain
        string status
        json collected_data
        timestamp collected_at
    }

    dsar_erasure_log {
        ulid id PK
        ulid request_id FK
        string domain
        string action_taken
        string scope
        string legal_exemption
        timestamp erased_at
        ulid executed_by FK
    }

    dsar_suppression_list {
        ulid id PK
        ulid company_id FK
        string email
        string reason
        timestamp suppressed_at
        ulid request_id FK
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `DSARRequestSubmitted` | Request created | Notifications (legal team), Legal (open request queue) |
| `DSARIdentityVerified` | ID check passes | Legal (start data collection), Notifications (requester) |
| `DSARDataCollectionRequested` | Per-domain trigger | Each domain (CRM, Finance, Marketing, etc.) |
| `DSARDataCollected` | Domain returns payload | Legal (aggregator compiles report) |
| `DSARReportReady` | All domains responded | Notifications (requester email with download link) |
| `DSAREraseCompleted` | All erasures done | Legal (close request), Notifications (requester confirmation) |
| `DSARDeadlineApproaching` | 7 days before deadline | Notifications (legal manager urgent alert) |

---

## Permissions

```
legal.dsar.view-any
legal.dsar.verify-identity
legal.dsar.process-erasure
legal.dsar.override-legal-hold
legal.dsar.export-report
legal.dsar.manage-suppression-list
```

---

## Competitors Displaced

DataGrail · OneTrust DSAR · Osano · Transcend · TrustArc · Manual email workflows

---

## Related

- [[MOC_Legal]]
- [[concept-multi-tenancy]] — `company_id` scopes all requests
- [[entity-contact]] — CRM contact is primary data subject
- [[entity-employee]] — employees are also data subjects (different regulation scope)
- [[MOC_CorePlatform]] — file storage for ID documents, notifications for alerts
