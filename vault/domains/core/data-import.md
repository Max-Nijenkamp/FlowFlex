---
type: module
domain: Core Platform
panel: app
module-key: core.import
status: planned
color: "#4ADE80"
---

# Data Import

> CSV import engine — upload a file, validate every row, preview errors, confirm, and import — used by every domain module to migrate existing data into FlowFlex.

**Panel:** `app`
**Module key:** `core.import`

## What It Does

The Data Import module provides a reusable CSV import pipeline that every domain module can plug into. A domain registers its import handler (column mapping, validation rules, row processor) and the core engine handles file upload, parsing, validation, error reporting, and confirmed import. The tenant user uploads a CSV, sees a preview of the first 20 rows with column mapping controls, reviews validation errors row by row, then confirms the import which runs as a queued batch job. Import history is retained so users can see what was imported and when.

## Features

### Core
- Filament custom page with file upload component (CSV only, max 10 MB)
- Column mapping step: CSV header rows mapped to FlowFlex field names via dropdown selectors
- Row-level validation: each row validated against the domain's rules; errors displayed with row number and field
- Preview mode: show first 20 rows with mapped values before confirming
- Queued batch import: confirmed imports run as a `Bus::batch()` of chunk jobs so large files (10,000+ rows) don't time out
- Import history table: timestamp, file name, row count, success count, error count, status

### Advanced
- Duplicate detection per domain: configurable deduplication key (e.g. `email` for contacts, `employee_number` for employees) — warn on duplicates, skip or overwrite configurable
- Rollback: import job ID stored; failed imports can be rolled back by soft-deleting all records created in that batch
- Domain-specific import handlers: each domain registers via `ImportRegistry::register('employees', EmployeeImportHandler::class)` — core engine is completely generic
- Error CSV download: after a failed or partial import, download a CSV of only the rows that failed with error descriptions added as a last column

### AI-Powered
- Auto column mapping: ML model matches CSV column headers to FlowFlex field names — pre-selects the most likely mapping so users rarely need to adjust manually
- Data quality scoring: before confirming, show a quality score (e.g. "87% of rows will import cleanly — 13% have issues") with actionable suggestions

## Data Model

```erDiagram
    import_jobs {
        ulid id PK
        ulid company_id FK
        string domain
        string entity_type
        string file_path
        string status
        integer total_rows
        integer success_rows
        integer error_rows
        json column_mapping
        ulid created_by FK
        timestamp completed_at
        timestamps created_at/updated_at
    }

    import_errors {
        ulid id PK
        ulid import_job_id FK
        integer row_number
        string field
        string error
        json row_data
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `domain` | Which domain module owns this import (e.g. `hr`, `crm`) |
| `entity_type` | Which entity is being imported (e.g. `employees`, `contacts`) |
| `status` | pending / validating / confirmed / importing / complete / failed |
| `column_mapping` | JSON map of CSV column → FlowFlex field |

## Permissions

- `core.import.upload`
- `core.import.preview`
- `core.import.confirm`
- `core.import.view-history`
- `core.import.rollback`

## Filament

- **Resource:** None
- **Pages:** `DataImportPage` — multi-step custom page: upload → map → validate → confirm → status
- **Custom pages:** `DataImportPage`
- **Widgets:** None
- **Nav group:** Tools (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zapier | CSV → SaaS data migration flows |
| Fivetran | Data ingestion from CSV files |
| HubSpot | Contact and deal CSV import |
| BambooHR | Employee record CSV import |

## Implementation Notes

**Filament:** `DataImportPage` is a multi-step custom `Page` using Filament's `HasWizard` pattern — five steps: (1) Select entity type (dropdown), (2) Upload CSV (Filament `FileUpload` component, CSV only, max 10 MB, stored temporarily in S3), (3) Column mapping (dynamically rendered `Select` fields — one per detected CSV header, options are the target entity's field names), (4) Validation preview (table of first 20 rows with inline error indicators), (5) Confirm & import (submit triggers `Bus::batch()` dispatch).

**ImportRegistry pattern:** Each domain registers its import handler in its `ServiceProvider`. The registry stores a map of `entity_type => ImportHandlerClass`. On `DataImportPage::mount()`, the available entity types are read from `ImportRegistry::all()` filtered to modules active for the current company. The handler class defines: `columnDefinitions()` (returns the available target fields), `validateRow(array $row): array $errors`, and `processRow(array $row): Model`.

**Queued batch import:** Use `Bus::batch()` with chunked jobs (chunk size: 100 rows per job). The batch ID is stored in `import_jobs.batch_id` (add this column). `ImportBatchFinishedJob` (the batch's `then` callback) updates the `import_jobs.status` to `complete` and sends `ImportCompleteNotification` to the user who initiated the import.

**Rollback:** Rollback is implemented by soft-deleting all records in the batch (not hard-delete). Each imported record must store its `import_job_id` so the rollback query is `Model::where('import_job_id', $jobId)->delete()`. Add `import_job_id ulid FK nullable` to every importable entity's table (employees, contacts, etc.) — this column is populated only when the record was created via an import job.

**AI auto column mapping:** Uses `app/Services/AI/ColumnMappingService.php` — sends the CSV header row and the list of target field names to OpenAI GPT-4o and receives a JSON mapping suggestion. This is called asynchronously after CSV upload; the mapping step pre-fills the dropdowns but the user can override.

**Missing from data model:** `import_jobs` needs `ulid batch_id nullable` to track the Laravel Bus batch for progress polling. Progress polling: the import status page polls `Bus::findBatch($batchId)->progress()` via a Livewire polling directive (`wire:poll.2000ms`).

## Related

- [[file-storage]]
- [[audit-log]]
- [[notifications]]
