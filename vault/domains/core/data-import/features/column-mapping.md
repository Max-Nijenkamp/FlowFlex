---
domain: core
module: data-import
feature: column-mapping
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Column Mapping

Parent: [[../_module]] · See [[../architecture]] · [[../api]]

The create flow is an upload + mapping wizard: after uploading a CSV/XLSX, the user maps source columns to the target importer's fields.

- The target's importer supplies the expected template; **all required fields must be mapped** or create is rejected (validated on `CreateImportData.column_map`).
- The mapping is persisted on `data_imports.column_map` (jsonb).
- **Validation preview**: the first 10 rows are shown with pass/fail status *before* committing — the preview validates without writing.

## UI

- **Kind**: custom-page
- **Page**: Import wizard — `DataImportResource` Create page (`/app/data-imports/create`) rendered as a multi-step wizard *(assumed page slug)*
- **Layout**: Step 1 upload dropzone (CSV/XLSX) + target picker (populated from `ImporterRegistry::available()`); Step 2 mapping grid — one row per source column with a select bound to the target importer's fields, required fields flagged; Step 3 validation-preview table showing the first 10 rows with a green/red pass-fail chip per row and per-column error text.
- **Key interactions**: pick a target → upload a file → app parses the header row → map each source column to a target field → click "Preview" to validate the first 10 rows read-only → resolve any required-field gaps → "Start import" dispatches the background job.
- **States**: empty = no file chosen (dropzone prompt) · loading = file parsing / preview validation spinner · error = unmapped required column or unreadable file (inline field error, create blocked) · selected = a source column highlighted with its chosen mapping.
- **Gating**: `core.import.create` (+ `BillingService::hasModule('core.import')`).

## Data

- Owns / writes: `data_imports` (this module's only table) — the create action writes the row incl. `column_map` (jsonb), `target`, `filename`, `status=pending`.
- Reads: the target importer's template + required fields via `ImporterRegistry` / `ImporterInterface` (read-only); per-row preview validation calls the target module's Create DTO (read-only, no write).
- Cross-domain writes: none from this feature — actual target rows are written by the target module's importer inside `ProcessImportJob`, not by column-mapping. This feature never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (module fires/consumes no domain events).
- Feeds: none.
- Shared entity: importer templates + required-field definitions are owned by each target domain module (e.g. `hr.employees`, `crm.contacts`); this feature reads them read-only via the registry.
