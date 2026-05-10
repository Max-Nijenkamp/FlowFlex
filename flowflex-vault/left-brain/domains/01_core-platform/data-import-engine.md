---
type: module
domain: Core Platform
panel: admin
cssclasses: domain-admin
phase: 1
status: in-progress
migration_range: 010001–019999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# Data Import Engine

Centralised CSV/Excel import infrastructure used by every domain. Customers migrating from old tools (Xero, Salesforce, BambooHR, Shopify) bulk-import their existing data. Without this, onboarding friction is too high for companies with existing data.

**Panel:** `admin` (shared) + each domain panel surfaces importer for own entities  
**Phase:** 1 — needed at onboarding before any domain is useful

---

## Features

### Universal Import Flow
1. Download template (CSV with correct headers + example row + validation rules inline)
2. Upload file (CSV, XLSX up to 50k rows)
3. Column mapping UI — drag-and-drop map uploaded columns to FlowFlex fields
4. Validation preview — show errors row-by-row before committing
5. Dry run — simulate import, show count of records that will be created/updated/skipped
6. Commit — run actual import as background job
7. Result summary — imported / skipped / failed with downloadable error log

### Supported Import Types (per domain)
| Domain | Importable Entities |
|---|---|
| Core | Users |
| HR | Employees, Leave Balances |
| Finance | Invoices (open), Customers, Suppliers, Chart of Accounts, Opening Balances |
| CRM | Contacts, Companies, Deals |
| Operations | Products, Inventory Stock Levels, Suppliers |
| Ecommerce | Products, Orders (historical), Customers |
| Projects | Projects, Tasks |
| Marketing | Email Contacts / Lists |

### Validation Rules Engine
- Required fields check
- Type validation (date formats, numeric, email)
- Duplicate detection (by unique key per entity — email for contacts, SKU for products)
- Relationship validation (foreign key lookups — does the assigned user exist?)
- Custom per-entity business rules (e.g. invoice date cannot be future)
- Error row highlighted with column-level error message

### Duplicate Handling Strategies
- `skip` — ignore row if duplicate key found
- `update` — merge into existing record
- `error` — fail row, report to user

### Rollback
- Failed mid-import: partial rollback to last consistent state (uses DB transactions per batch of 100 rows)
- Committed import: rollback within 24h window (soft-deletes all created records from that import job)
- Import job ID stamped on every created record for traceability

### Import History
- Log of all past imports (date, entity type, uploaded by, rows imported, rows failed)
- Re-download original uploaded file
- Re-run with same mapping

---

## Data Model

```erDiagram
    import_jobs {
        ulid id PK
        ulid company_id FK
        ulid created_by FK
        string entity_type
        string status
        string original_filename
        string storage_path
        json column_mapping
        integer rows_total
        integer rows_imported
        integer rows_skipped
        integer rows_failed
        json error_summary
        timestamp committed_at
        timestamp rolled_back_at
    }
```

---

## Permissions

```
core.import.create
core.import.rollback
core.import.view-history
```

---

## Related

- [[MOC_CorePlatform]]
- [[entity-company]]
- [[MOC_Finance]] — opening balances, invoice import
- [[MOC_CRM]] — contact import from CSV/CRM export
