---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.privacy
status: planned
priority: v1
depends-on: [core.settings, foundation.queues, core.files, core.rbac, core.billing]
soft-depends: []
fires-events: [DSARRequestSubmitted]
consumes-events: []
patterns: [gdpr, queues, states]
tables: [dsar_requests, consent_logs]
permission-prefix: core.privacy
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Data Privacy

GDPR tooling: DSAR (Data Subject Access Request) management, consent logs, full dataset export, and erasure queue. Ensures compliance from day one. Implements the policy defined in [[architecture/data-lifecycle]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/company-settings\|core.settings]] | retention config, DSAR email |
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | export + erasure jobs |
| Hard | [[domains/core/file-storage\|core.files]] | export ZIP storage |
| Hard | [[domains/core/rbac\|core.rbac]] + [[domains/core/billing-engine\|core.billing]] | permissions + gating |

---

## Core Features

- DSAR queue: log and process data access and erasure requests from employees or customers
- Data export: full company dataset export as ZIP (CSV per model type) — available to owner
- Erasure workflow: soft-delete → anonymise → schedule hard delete — cascade rules per table family from [[architecture/data-lifecycle]] (legal holds: financial records kept)
- `PersonalDataRegistry`: modules register their PII tables/fields — drives both export scope and erasure cascade
- Consent log: track when consent was given/withdrawn per data category per user
- DSAR contact email configurable in Company Settings
- Retention policy: configurable retention period per data type (only lengthening beyond statutory minimums)
- DSAR response deadline tracker: 30-day countdown per request

---

## Data Model

### dsar_requests

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, indexed | |
| subject_email | string | not null | |
| request_type | string | not null | access / erasure |
| status | string | not null, default `received` | state machine |
| due_at | timestamp | not null | created + 30 days |
| completed_at | timestamp | nullable | |
| result_path | string | nullable | export ZIP (access requests) |
| deleted_at | timestamp | nullable | kept as compliance proof — never purged with company data |

### consent_logs

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), user_id FK | ulid | |
| data_category | string | |
| consented_at | timestamp | |
| withdrawn_at | timestamp nullable | |

---

## State Machine

Column: `dsar_requests.status`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `received` | `in-progress` | `core.privacy.process` | fires `DSARRequestSubmitted` on create (not transition) |
| `in-progress` | `completed` | export/erasure job success | `completed_at`, requester notified |
| `in-progress` | `rejected` | `core.privacy.process` (identity not verified / legal hold) | reason recorded |

---

## DTOs

### CreateDsarRequestData (input)
| Field | Type | Validation |
|---|---|---|
| subject_email | string | required, email |
| request_type | string | required, in:access,erasure |

## Services & Actions

- `PersonalDataRegistry::register(string $moduleKey, array $tablesFields)` / `tablesFor(string $email)` — each module registers in its ServiceProvider
- `ProcessAccessRequestJob` (`exports` queue, `WithCompanyContext`) — collects registry tables → ZIP of CSVs → `result_path`, marks completed
- `ProcessErasureRequestJob` (`default` queue) — applies per-family cascade rules ([[architecture/data-lifecycle]]); chunked, idempotent (anonymise ops are naturally re-runnable)
- `ExportCompanyDataAction::run(): string` — full company export (data portability), owner-triggered

## Events

### Fires: DSARRequestSubmitted
| Payload field | Type |
|---|---|
| company_id | string |
| dsar_request_id | string |
| request_type | string |
| subject_email | string |
| due_at | CarbonImmutable |

Consumers per [[architecture/event-bus]] (Notifications now; Legal in P3).

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DsarRequestResource` | #1 CRUD resource | deadline countdown column, process/reject actions, result download |
| `DataExportPage` | #7 custom page | trigger full export, download when ready (polling 30s) |

---

## Permissions

`core.privacy.view-any` · `core.privacy.create` · `core.privacy.process` · `core.privacy.export`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessAccessRequestJob` | exports | on demand | regenerates ZIP, overwrites path — safe |
| `ProcessErasureRequestJob` | default | on demand | anonymise writes idempotent; per-family ordering FK-safe |
| `DsarDeadlineReminderCommand` | notifications | daily | notifies on due_at-7d and due_at-1d, WHERE guards |
| `PurgeCancelledCompaniesCommand` | default | daily | per data-lifecycle company purge, chunked + logged |

---

## Test Checklist

- [ ] Tenant isolation: DSAR of company A invisible to company B
- [ ] Module gating + permission gating on both surfaces
- [ ] Access request produces ZIP containing rows for the subject across registered tables only
- [ ] Erasure: hr_employees anonymised per rule, invoices untouched, emergency contacts hard-deleted
- [ ] Erasure with open legal hold (employment ongoing) → rejected path
- [ ] DSAR rows survive (status updated) — compliance proof retained
- [ ] Deadline reminders fire at -7d/-1d once each
- [ ] Company purge respects 90-day window and keeps FlowFlex-issued invoices

---

## Build Manifest

```
database/migrations/xxxx_create_dsar_requests_table.php
database/migrations/xxxx_create_consent_logs_table.php
app/Models/Core/{DsarRequest,ConsentLog}.php
app/States/Core/DsarRequest/{DsarRequestState,Received,InProgress,Completed,Rejected}.php
app/Data/Core/CreateDsarRequestData.php
app/Support/Privacy/PersonalDataRegistry.php
app/Jobs/Core/{ProcessAccessRequestJob,ProcessErasureRequestJob}.php
app/Actions/Core/ExportCompanyDataAction.php
app/Events/Core/DSARRequestSubmitted.php
app/Console/Commands/Core/{DsarDeadlineReminderCommand,PurgeCancelledCompaniesCommand}.php
app/Filament/App/Resources/DsarRequestResource.php
app/Filament/App/Pages/DataExportPage.php
database/factories/Core/DsarRequestFactory.php
tests/Feature/Core/{DsarAccessTest,DsarErasureTest,CompanyPurgeTest}.php
```

---

## Related

- [[architecture/data-lifecycle]] — the policy this implements
- [[domains/core/company-settings]]
- [[product/pricing-model]] — GDPR section
- [[architecture/event-bus]]
