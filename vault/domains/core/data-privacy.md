---
type: module
domain: Core Platform
panel: app
module-key: core.privacy
status: planned
color: "#4ADE80"
---

# Data Privacy

> GDPR, CCPA, and EU pay transparency compliance in one dashboard — data subject requests, consent records, processing register, cookie configuration, and breach notifications — without OneTrust.

**Panel:** `/app`
**Module key:** `core.privacy`

## What It Does

Data Privacy gives every FlowFlex company a structured compliance toolkit for the major data protection frameworks. A CS team member or Privacy Officer can manage incoming data subject requests (right to erasure, right of access, right to portability) with enforced 30-day timers, maintain a record of processing activities for regulatory audits, configure the cookie consent banner for the Vue 3 frontend, register Data Processing Agreements with third-party processors, and log any data breaches with the 72-hour GDPR notification clock. The module is always-on (Core Platform) — no activation required — and every other FlowFlex domain routes privacy events through it.

## Features

### Core
- Data Subject Request (DSR) management: receive, track, and fulfil requests for erasure (right to be forgotten), access (SAR — Subject Access Request), and portability (export of personal data in machine-readable format); each request has a 30-day statutory clock that counts down visibly with automated reminders at 10 days and 3 days remaining
- Consent record management: record the legal basis for processing each category of personal data per contact — consent captured through forms, legitimate interest documented for other processing activities; full consent history with timestamps
- Data processing register: a structured register of all processing activities (as required by GDPR Article 30) — each entry documents: activity name, purpose, legal basis, data categories processed, retention period, internal and third-party processors, and risk level
- Privacy notice version management: upload and version privacy notices and terms of service — each version is timestamped and stored; the current published version is linked from the Vue 3 frontend

### Advanced
- Cookie consent configuration: configure the cookie consent banner for the company's Vue 3 frontend — categorise cookies (essential, analytics, marketing, preferences) and define which scripts load conditionally based on consent; the banner configuration is served via the FlowFlex CDN to the Vue 3 frontend via a configuration endpoint
- Data Processing Agreement (DPA) register: record agreements with all third-party data processors (e.g. Twilio, Stripe, OpenAI) — stores processor name, contact, DPA document URL, data categories shared, and annual review date; reminder notifications 30 days before review date
- Privacy breach notification log: document data incidents with discovery date, description, affected data categories, estimated affected subject count, and timestamps for: supervisory authority notification (72-hour GDPR obligation), and data subject notification — overdue notifications trigger a high-priority alert on the compliance dashboard
- DSR fulfilment tools: for erasure requests, a guided checklist of all FlowFlex modules that hold data for the subject — each module exposes a `PrivacyHook::eraseSubject($contactId, $companyId)` method; the Data Privacy module calls all registered hooks and records which completed successfully
- Data portability export: for access/portability requests, generate a structured JSON export of all personal data held about a subject across all FlowFlex modules that have registered a `PrivacyHook::exportSubject()` method

### AI-Powered
- Request categorisation: when a DSR is received via the intake email form, AI classifies the request type (erasure / access / portability / objection / correction) and pre-fills the request record — a Privacy Officer reviews and confirms before the clock starts
- Processing register gap analysis: AI compares the company's list of active FlowFlex modules against the processing register and identifies modules that process personal data but have no corresponding register entry — surfaces as a "Gaps to Document" list
- Consent coverage report: AI generates a plain-English summary of the company's consent coverage — which data categories are well-documented, which are missing legal basis records, and which have a high percentage of withdrawn consent — suitable for a Data Protection Officer's quarterly review

## Data Model

```erDiagram
    privacy_dsr_requests {
        ulid id PK
        ulid company_id FK
        enum type
        string requestor_email
        string requestor_name
        enum status
        timestamp due_at
        timestamp completed_at "nullable"
        text notes
        json fulfilment_log "nullable"
        ulid assigned_to FK "nullable"
        timestamps created_at/updated_at
    }

    privacy_consent_records {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string data_category
        enum legal_basis
        timestamp consented_at
        timestamp withdrawn_at "nullable"
        string source
        text notes
        timestamps created_at/updated_at
    }

    privacy_processing_register {
        ulid id PK
        ulid company_id FK
        string activity_name
        text purpose
        enum legal_basis
        json data_categories
        integer retention_days
        json processors
        enum risk_level
        timestamps created_at/updated_at
    }

    privacy_breaches {
        ulid id PK
        ulid company_id FK
        timestamp discovered_at
        text description
        json data_categories_affected
        integer affected_count_estimated
        enum severity
        timestamp notified_supervisory_authority_at "nullable"
        timestamp notified_subjects_at "nullable"
        text remediation_taken
        ulid reported_by FK
        timestamps created_at/updated_at
    }

    privacy_notices {
        ulid id PK
        ulid company_id FK
        string title
        enum type
        string version
        text content
        boolean is_published
        timestamp published_at "nullable"
        timestamps created_at/updated_at
    }

    privacy_dpa_register {
        ulid id PK
        ulid company_id FK
        string processor_name
        string contact_email
        string dpa_document_url
        json data_categories_shared
        date annual_review_date
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `privacy_dsr_requests.type` | enum: `erasure` / `access` / `portability` / `objection` / `correction` |
| `privacy_dsr_requests.status` | enum: `received` / `in_progress` / `completed` / `refused` / `overdue` |
| `privacy_dsr_requests.due_at` | Set to `received_at + 30 days` (GDPR/CCPA requirement); a scheduled job sets `status = overdue` when `now() > due_at` and `status != completed` |
| `privacy_dsr_requests.fulfilment_log` | JSON array recording each module's erasure/export hook result: `[{module, status, completed_at, error}]` |
| `privacy_consent_records.legal_basis` | enum: `consent` / `legitimate_interest` / `contract` / `legal_obligation` / `vital_interests` / `public_task` |
| `privacy_breaches.severity` | enum: `low` / `medium` / `high` / `critical` |
| `privacy_notices.type` | enum: `privacy_policy` / `cookie_policy` / `terms_of_service` / `dpa` |

## Permissions

```
core.privacy.view-dsrs
core.privacy.manage-dsrs
core.privacy.manage-consent-records
core.privacy.manage-processing-register
core.privacy.manage-breaches
```

## Filament

- **Resource:** `DsrRequestResource` — list of all data subject requests with columns: type badge, requestor email, status badge, days remaining (countdown in red when <5 days), assigned to; row action "Fulfil" opens a step-by-step wizard (confirm identity, run erasure/export hooks, record completion); "Refuse" action records refusal reason
- **Resource:** `ConsentRecordResource` — list of all consent records filterable by contact, legal basis, data category, and withdrawn status; read-only for audit purposes; new records are created by forms and automations, not manually
- **Resource:** `ProcessingRegisterResource` — CRUD for processing activities with full form; includes a "Check for gaps" action that triggers the AI gap analysis
- **Resource:** `PrivacyBreachResource` — CRUD for breach incidents with urgency indicators: red banner if `notified_supervisory_authority_at` is null and `discovered_at` is more than 60 hours ago (12-hour warning before 72-hour deadline)
- **Custom dashboard page:** `PrivacyComplianceDashboardPage` — the entry point for the module; shows: DSR overdue count (red alert), DSR due this week, open breaches with authority notification outstanding, processing register gap count, DPA reviews due in next 30 days, consent withdrawal rate (last 30 days)
- **Nav group:** Settings (app panel) — visible only to users with `core.privacy.view-dsrs` permission

## Displaces

| Competitor | Feature Displaced |
|---|---|
| OneTrust (SMB tier) | DSR management, processing register, consent records |
| Osano | Cookie consent configuration and DSR tracking |
| Cookiebot | Cookie consent banner management |
| TrustArc | Privacy compliance workflow and assessment tools |
| DataGrail | Data subject request automation |

## Related

- [[company-settings]]
- [[audit-log]]
- [[webhooks]]
- [[../hr/employee-profiles]]
- [[../crm/contacts]]

## Implementation Notes

### PrivacyHook Registration Pattern
Every domain that processes personal data must register a `PrivacyHook` handler with the Data Privacy module. The handler implements two methods:
- `eraseSubject(string $contactId, string $companyId): PrivacyHookResult` — permanently deletes or anonymises all personal data for the contact
- `exportSubject(string $contactId, string $companyId): array` — returns all personal data as a structured array for inclusion in the portability export

Handlers are registered in each domain's ServiceProvider via `PrivacyHookRegistry::register('domain.module', MyPrivacyHook::class)`. The DSR fulfilment wizard calls all registered handlers in sequence and records results in `fulfilment_log`. Handlers must be idempotent — if called twice for the same subject, the second call must not fail.

### Erasure vs Anonymisation
"Right to erasure" does not always mean deletion — it means the data must no longer be identifiable. For records that are legally required to be retained (e.g. financial transaction records under accounting law), erasure means anonymisation (replace name, email, and identifiers with `[DELETED]` or a hash). The handler for each domain must decide the correct approach per record type and document it in the processing register.

### Cookie Consent Architecture
The cookie consent banner is a pre-built web component served from `//cdn.flowflex.com/cookie-consent/v1/consent.js`. The banner configuration (categories, scripts to conditionally load, style) is fetched from `GET /api/v1/cookie-consent/{company_slug}` — a public API endpoint requiring no auth. Consent choices are stored in `localStorage` and in a first-party cookie (`_ffconsent`) — the same mechanism used by IAB TCF-compliant CMPs. Consent records are not written to `privacy_consent_records` for cookie consent (too high volume) — they are stored client-side only; only explicit form-based consents are written to the database.

### 72-Hour Breach Clock
A scheduled job runs every hour checking `privacy_breaches` where `notified_supervisory_authority_at IS NULL` and `discovered_at < NOW() - INTERVAL '60 hours'`. These records trigger a `PrivacyBreachWarningNotification` sent to all users with `core.privacy.manage-breaches` permission. At 71 hours, the notification escalates to a system-level alert visible on every Filament page for those users.
