---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.import
status: complete
priority: v1
depends-on: [core.files, foundation.queues, core.billing, core.rbac]
soft-depends: [hr.profiles, crm.contacts]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [data_imports]
permission-prefix: core.import
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Data Import

CSV/Excel import for bulk data entry across domains. Column mapping UI, validation preview, error reporting, and background processing via Horizon.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/file-storage\|core.files]] | uploaded files stored tenant-scoped |
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | `imports` queue |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/employee-profiles\|hr.profiles]], [[domains/crm/contacts\|crm.contacts]] | import targets — a target's importer ships with that module; import UI lists only targets whose module is active |

---

## Core Features

- CSV and XLSX upload with column mapping UI
- Validation preview: show first 10 rows with pass/fail status before committing
- Background import job via Horizon (`imports` queue) — chunked per [[architecture/queue-jobs]], no timeout issues
- Import templates available per domain (employee template, contact template, product template)
- Error report: downloadable CSV of failed rows with error messages per column — import never aborts on row failure
- Import history: timestamp, domain, row count, success rate
- Importer registry: each domain module registers its importer class + template (employees, contacts, expense items, products)

---

## Data Model

### data_imports

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, indexed | |
| target | string | not null | importer key, e.g. `hr.employees` |
| filename | string | not null | original name |
| status | string | not null, default `pending` | state machine |
| column_map | jsonb | not null | source column → field |
| total_rows / success_rows / error_rows | int | default 0 | |
| error_report_path | string | nullable | tenant-scoped file |
| imported_by | ulid | FK users | |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, created_at)`

---

## State Machine

Column: `data_imports.status`.

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `pending` | `processing` | job picked up | |
| `processing` | `complete` | all chunks done | notification to importer |
| `processing` | `failed` | infrastructure failure (not row errors) | notification + error log |

Row-level errors do NOT fail the import — they land in the error report.

---

## DTOs

### CreateImportData (input)
| Field | Type | Validation |
|---|---|---|
| target | string | required, in registered importer keys, module active |
| file | UploadedFile | required, mimes:csv,xlsx, max per settings |
| column_map | array<string,string> | required fields of the target all mapped |

## Services & Actions

- `ImporterRegistry::register(string $key, class-string $importer)` / `available(): array` (filters by `hasModule`)
- `StartImportAction::run(CreateImportData $data): DataImport` — stores file, dispatches `ProcessImportJob`
- `ProcessImportJob` — `imports` queue, `WithCompanyContext`, chunked rows, per-row validate via the target module's Create DTO, per-row try/catch (idempotency rules per [[architecture/queue-jobs]])

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DataImportResource` | #1 CRUD resource | create = upload + mapping wizard steps; view = progress + counts + error report download |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('core.import.view-any') && BillingService::hasModule('core.import')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a throttle limiter on the import-create surface (e.g. a low-rate 'import' limiter) in the Filament/Actions section.
- **Upload contract** (medium): Note in Core Features / DTOs that the uploaded import file is stored via FileStorageService under companies/{company_id}/ (no raw Storage::put), matching the file-storage path contract.

---

## Permissions

`core.import.view-any` · `core.import.create`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessImportJob` | imports | on demand | rows upserted on target natural key where the importer defines one; otherwise duplicate-guard per importer *(assumed)* |

---

## Test Checklist

- [ ] Tenant isolation: import rows land under the importing company only
- [ ] Module gating: target list excludes inactive modules; resource gated
- [ ] Preview validates first 10 rows without writing
- [ ] Row failure recorded in error report; import continues; counts correct
- [ ] Unmapped required column rejected at create
- [ ] Large file processes chunked on `imports` queue (no memory spike)

---

## Build Manifest

```
database/migrations/xxxx_create_data_imports_table.php
app/Models/Core/DataImport.php
app/States/Core/DataImport/{DataImportState,Pending,Processing,Complete,Failed}.php
app/Data/Core/CreateImportData.php
app/Support/Import/{ImporterRegistry,ImporterInterface}.php
app/Actions/Core/StartImportAction.php
app/Jobs/Core/ProcessImportJob.php
app/Filament/App/Resources/DataImportResource.php
database/factories/Core/DataImportFactory.php
tests/Feature/Core/{DataImportTest,ImportErrorReportTest}.php
```

---

## Related

- [[domains/core/file-storage]]
- [[architecture/queue-jobs]] — chunking + idempotency rules
- [[architecture/packages]] (`maatwebsite/laravel-excel`)
