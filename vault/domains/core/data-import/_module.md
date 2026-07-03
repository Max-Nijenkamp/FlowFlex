---
domain: core
module: data-import
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Data Import

CSV/Excel import for bulk data entry across domains. Column-mapping UI, validation preview, downloadable error reporting, and background processing on the `imports` Horizon queue.

## Module-key

`core.import`

**Priority:** v1  
**Panel:** app  
**Permission prefix:** `core.import`  
**Tables:** `data_imports`  
**Events:** fires none · consumes none

## Sibling notes

- [[architecture]] — registry, action, job, state machine + flow diagram
- [[data-model]] — `data_imports` table + ERD + state table
- [[api]] — `CreateImportData` DTO + `ImporterInterface` contract
- [[security]] — rate limiter, upload path contract, tenant isolation, gating
- [[unknowns]] — UNVERIFIED / `*(assumed)*` items
- Features: [[features/column-mapping]] · [[features/error-report]] · [[features/importer-registry]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../file-storage/_module]] | uploaded files stored tenant-scoped |
| Hard | [[../../foundation/queue-workers/_module]] | `imports` queue |
| Hard | [[../billing-engine/_module]] + [[../rbac/_module]] | gating + permissions |
| Soft | [[../../hr/employee-profiles/_module]], [[../../crm/contacts/_module]] | import targets — a target's importer ships with that module; import UI lists only targets whose module is active |

> [!warning] UNVERIFIED — needs confirmation: exact folder slugs for the queue-workers, hr and crm dependency modules (linked by convention).

## Core Features

- CSV and XLSX upload with column mapping UI
- Validation preview: show first 10 rows with pass/fail status before committing
- Background import job via Horizon (`imports` queue) — chunked per [[../../../architecture/queue-jobs]], no timeout issues
- Import templates available per domain (employee template, contact template, product template)
- Error report: downloadable CSV of failed rows with error messages per column — import never aborts on row failure
- Import history: timestamp, domain, row count, success rate
- Importer registry: each domain module registers its importer class + template (employees, contacts, expense items, products)
- Uploaded files are stored via `FileStorageService` under `companies/{company_id}/` (no raw `Storage::put`) — see [[security]]

## Test Checklist

- [ ] Tenant isolation: import rows land under the importing company only
- [ ] Module gating: target list excludes inactive modules; resource gated
- [ ] Preview validates first 10 rows without writing
- [ ] Row failure recorded in error report; import continues; counts correct
- [ ] Unmapped required column rejected at create
- [ ] Large file processes chunked on `imports` queue (no memory spike)

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_data_imports_table.php
app/Models/DataImport.php
app/States/DataImport/{DataImportState,Pending,Processing,Complete,Failed}.php
app/Data/CreateImportData.php
app/Support/Import/{ImporterRegistry,ImporterInterface}.php
app/Actions/StartImportAction.php
app/Jobs/ProcessImportJob.php
app/Filament/App/Resources/DataImportResource.php
database/factories/DataImportFactory.php
tests/Feature/Core/{DataImportTest,ImportErrorReportTest}.php
```

Spec listed `app/.../Core/...` and `database/factories/Core/...`; real layout is flat (no `Core/` subdir) — corrected above.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| — | none | — | This module fires and consumes no domain events. Cross-module coupling is via the `ImporterInterface` contract (target modules register importers) and read-only calls to target Create DTOs for validation. |

Data ownership: data-import owns and writes only `data_imports`, reads target importer templates/required-fields and target Create DTOs read-only, reads/writes tenant-scoped files via `FileStorageService`, and effects other domains only through the target module's own importer inside `ProcessImportJob` (the target module writes its own tables) ([[../../../security/data-ownership]]).

## Related

- [[../file-storage/_module]]
- [[../../../architecture/queue-jobs]] — chunking + idempotency rules
- [[../../../architecture/packages]] (`maatwebsite/laravel-excel`)
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../glossary]]
