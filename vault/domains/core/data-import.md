---
type: module
domain: Core Platform
panel: app
module-key: core.import
status: planned
color: "#4ADE80"
---

# Data Import

CSV/Excel import for bulk data entry across domains. Column mapping UI, validation preview, error reporting, and background processing via Horizon.

---

## Core Features

- CSV and XLSX upload with column mapping UI
- Validation preview: show first 10 rows with pass/fail status before committing
- Background import job via Horizon — no timeout issues for large files
- Import templates available per domain (employee template, contact template, product template)
- Error report: downloadable CSV of failed rows with error messages per column
- Import history: timestamp, domain, row count, success rate
- Domains that support import: HR (employees), CRM (contacts, companies), Finance (expense items), E-commerce (products)

---

## Data Model

| Table | Key Columns |
|---|---|
| `data_imports` | company_id, domain, filename, status (pending/processing/complete/failed), total_rows, success_rows, error_rows, imported_by, created_at |

---

## Filament

**`/app` panel:**
- `DataImportResource` — list, create (upload + map), view results

---

## Related

- [[domains/core/file-storage]]
