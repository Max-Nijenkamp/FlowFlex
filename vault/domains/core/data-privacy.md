---
type: module
domain: Core Platform
panel: app
module-key: core.privacy
status: planned
color: "#4ADE80"
---

# Data Privacy

GDPR tooling: DSAR (Data Subject Access Request) management, consent logs, full dataset export, and erasure queue. Ensures compliance from day one.

---

## Core Features

- DSAR queue: log and process data access and erasure requests from employees or customers
- Data export: full company dataset export as ZIP (CSV per model type) — available to owner
- Erasure workflow: soft-delete → anonymise → schedule hard delete (90-day retention)
- Consent log: track when consent was given/withdrawn per data category per user
- DSAR contact email configurable in Company Settings
- Retention policy: configurable retention period per data type
- DSAR response deadline tracker: 30-day countdown per request

---

## Data Model

| Table | Key Columns |
|---|---|
| `dsar_requests` | company_id, subject_email, request_type (access/erasure), status, due_at, completed_at |
| `consent_logs` | company_id, user_id, data_category, consented_at, withdrawn_at |

---

## Filament

**`/app` panel:**
- `DsarRequestResource` — list, create, update DSAR requests; track status
- `DataExportPage` (custom page) — trigger full export, download when ready

---

## Related

- [[domains/core/company-settings]]
- [[product/pricing-model]] — GDPR section
