---
type: gap
severity: medium
category: feature
status: open
color: "#F97316"
discovered: 2026-05-10
discovered_in: data-import-engine
last_updated: 2026-05-10
---

# Gap: Data Import Engine — no Filament UI, no CSV parsing, no background job

## Context

`DataImportService` has `createJob()`, `parseAndStoreRows()`, `validate()`, and `rollback()`. The service works and is tested. But there is no user-facing UI and no real CSV/XLSX parsing.

## The Problem

Missing pieces:
1. **No Filament resource** — no UI to upload a file, map columns, preview validation, or commit
2. **No CSV/XLSX parsing** — `parseAndStoreRows()` receives a pre-parsed array; nothing reads an uploaded file
3. **No background job** — the spec says "commit → run actual import as background job"; no `ProcessImportJob` queue job exists
4. **No template download** — no endpoint to download a CSV template with correct headers
5. **No error log download** — no endpoint to download failed rows as CSV

## Impact

Customers cannot bulk-import data. Every Phase 2 domain (HR employees, Finance invoices, CRM contacts) depends on this for onboarding migrations. High customer-impact blocker for enterprise onboarding.

## Proposed Solution

1. `app/Filament/App/Resources/ImportJobResource.php` — upload step (FileUpload), mapping step (Livewire column mapper), preview step, commit button
2. `ProcessImportJob` queue job — reads rows from `import_job_rows`, applies column mapping, validates, inserts in batches of 100
3. Use `league/csv` or `PhpSpreadsheet` for file parsing (check if either is in composer.json)
4. Template download route
5. Error CSV download via `Symfony\Component\HttpFoundation\StreamedResponse`

## Links

- Source builder log: [[core-platform-phase1]]
- Related spec: [[data-import-engine]]
