---
type: module
domain: Document Management
panel: dms
module-key: dms.retention
status: planned
color: "#4ADE80"
---

# Document Retention

> Retention policies — define schedules by document type, auto-archive on expiry, and legal hold management.

**Panel:** `dms`
**Module key:** `dms.retention`

---

## What It Does

Document Retention enforces the company's document lifecycle obligations. Compliance and legal teams define retention schedules for each document category (e.g. HR records 7 years, contracts 10 years, financial records 7 years) and the action to take on expiry (archive, review, or delete). The system tracks the retention clock for every document from its creation or approval date, sends reminders before expiry, and executes the configured action automatically. Legal hold prevents deletion or archiving of documents relevant to litigation or regulatory investigation.

---

## Features

### Core
- Retention schedule creation: document category, retention period (months), and expiry action (archive/review/delete)
- Automatic clock: retention period starts from the document's creation date or approval date
- Expiry notification: notify the document owner 30 days before the retention period expires
- Auto-archive: move expired documents to an archive zone where they are read-only
- Auto-delete: permanently delete documents after a confirmed final review at expiry
- Retention report: list of all documents with their expiry dates sorted by soonest-first

### Advanced
- Legal hold: mark a document (or a folder) as under legal hold; blocks all archiving and deletion until hold is lifted
- Retention schedule audit: immutable log of when retention actions were taken on each document
- Regulatory framework mapping: tag schedules to the regulation that mandates the retention period (e.g. GDPR, SOX)
- Custom clock start: for some document types (e.g. contracts), the retention clock starts from the contract end date
- Batch retention review: review and confirm expiry actions for a batch of documents simultaneously

### AI-Powered
- Category classification: AI classifies uploaded documents to the correct retention category automatically
- Retention risk detection: flag documents that appear to have missed their retention schedule
- Regulatory gap analysis: identify document categories in the DMS that lack a defined retention schedule

---

## Data Model

```erDiagram
    retention_schedules {
        ulid id PK
        ulid company_id FK
        string document_category
        integer retention_months
        string expiry_action
        string clock_start_trigger
        json regulatory_tags
        boolean is_active
        timestamps created_at_updated_at
    }

    document_retention_records {
        ulid id PK
        ulid document_id FK
        ulid schedule_id FK
        ulid company_id FK
        date clock_start_date
        date expiry_date
        boolean is_on_legal_hold
        string status
        timestamp action_taken_at
        timestamps created_at_updated_at
    }

    retention_schedules ||--o{ document_retention_records : "governs"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `retention_schedules` | Retention policy rules | `id`, `company_id`, `document_category`, `retention_months`, `expiry_action`, `is_active` |
| `document_retention_records` | Per-document tracking | `id`, `document_id`, `schedule_id`, `expiry_date`, `is_on_legal_hold`, `status` |

---

## Permissions

```
dms.retention.view
dms.retention.manage-schedules
dms.retention.apply-legal-hold
dms.retention.execute-expiry
dms.retention.view-audit-log
```

---

## Filament

- **Resource:** `App\Filament\Dms\Resources\RetentionScheduleResource`
- **Pages:** `ListRetentionSchedules`, `CreateRetentionSchedule`, `EditRetentionSchedule`
- **Custom pages:** `ExpiringDocumentsPage`, `LegalHoldPage`, `RetentionAuditPage`
- **Widgets:** `ExpiringThisMonthWidget`, `LegalHoldCountWidget`
- **Nav group:** Governance

---

## Displaces

| Feature | FlowFlex | SharePoint | Box | OpenText |
|---|---|---|---|---|
| Retention schedules | Yes | Yes | Yes | Yes |
| Auto-archive on expiry | Yes | Yes | Yes | Yes |
| Legal hold | Yes | Yes | Yes | Yes |
| AI category classification | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[document-library]] — retention records track documents from the library
- [[document-workflows]] — approved documents start their retention clock
- [[legal/INDEX]] — legal hold triggered by legal team for relevant documents
