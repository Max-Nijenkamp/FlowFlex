---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.dei
status: planned
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac, core.privacy]
soft-depends: [hr.compensation, hr.recruitment]
fires-events: []
consumes-events: []
patterns: [encryption, custom-pages, gdpr]
tables: [hr_dei_attributes, hr_dei_snapshots]
permission-prefix: hr.dei
encrypted-fields: ["hr_dei_attributes.value"]
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# DEI Metrics

Diversity, Equity, and Inclusion metrics and reporting — representation, pay equity, and inclusion trends. Privacy-sensitive: aggregated reporting only, opt-in collection, encrypted at rest.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | attributes attach to employees |
| Hard | [[domains/core/data-privacy\|core.privacy]] | consent tracking is mandatory for collection |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/compensation-benefits\|hr.compensation]] | pay equity (band-level); section hidden without it |
| Soft | [[domains/hr/recruitment\|hr.recruitment]] | hiring funnel by dimension; hidden without it |

---

## Core Features

- Diversity dimensions: gender, age band, ethnicity (where legally collectable), disability status
- Representation reporting: composition by level, department, role
- Pay equity analysis: pay gap by dimension (median, adjusted — uses `salary_band`, never exact salaries)
- Hiring diversity: applicant → hire funnel by dimension
- Promotion equity: promotion rates by dimension
- Inclusion pulse: survey-based inclusion sentiment (P3 pulse-survey link *(assumed: out of v1 scope)*)
- **Aggregation threshold: never show groups smaller than N=5** *(assumed default, configurable)* — anonymity protection
- Jurisdiction-aware: only collect/report what's legal per company country (config map *(assumed: per-country allowed-dimension list in settings)*)
- Collection is employee opt-in via self-service, consent-logged via core.privacy

---

## Data Model

### hr_dei_attributes

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK | ulid | unique `(employee_id, dimension)` |
| dimension | string | gender / age-band / ethnicity / disability |
| 🔐 value | text | encrypted — never indexed, never filtered in SQL |
| consented_at | timestamp | consent log reference in core.privacy |

GDPR: hard-deleted on employee erasure; withdrawal of consent deletes the row.

### hr_dei_snapshots

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| period | string | e.g. `2026-Q2` |
| dimension | string | |
| breakdown | jsonb | AGGREGATED counts only, groups < N suppressed before storage |
| created_at | timestamp | |

Dashboards read snapshots only — never live decrypt-and-group over individuals at request time.

---

## DTOs

### SubmitDeiAttributesData (self-service, own only)
| Field | Type | Validation |
|---|---|---|
| attributes | array<{dimension, value}> | dimensions in jurisdiction-allowed set; values in dimension option list; consent checkbox required |

## Services & Actions

- `DeiSnapshotService::generate(string $period): void` — decrypts attribute set in a job, aggregates, suppresses groups < N, stores snapshot, discards individuals
- `SubmitOwnDeiAttributesAction::run(SubmitDeiAttributesData $data): void` — own-only, writes consent log
- `WithdrawDeiConsentAction::run(): void` — deletes own attributes + logs withdrawal

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `GenerateDeiSnapshotsCommand` | hr | quarterly | upsert per `(company, period, dimension)` |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DeiDashboardPage` | #6 dashboard page | snapshot-driven charts; "insufficient group size" placeholders |
| DEI section in self-service MyProfilePage | form section | opt-in collection + withdraw button |

---

## Permissions

`hr.dei.view-dashboard` (HR leadership) · `hr.dei.submit-own` (all employees)

No permission exposes individual attributes — not even `view-any`. Individual values are never rendered anywhere.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Values ciphertext in DB; no SQL path filters on `value`
- [ ] Snapshot suppresses groups smaller than N (boundary test at N-1/N)
- [ ] Dashboard renders from snapshots only (no live individual reads)
- [ ] Consent required to submit; withdrawal deletes attributes + logs
- [ ] Jurisdiction config blocks disallowed dimensions
- [ ] Employee erasure removes DEI attributes

---

## Build Manifest

```
database/migrations/xxxx_create_hr_dei_attributes_table.php
database/migrations/xxxx_create_hr_dei_snapshots_table.php
app/Models/HR/{DeiAttribute,DeiSnapshot}.php
app/Data/HR/SubmitDeiAttributesData.php
app/Services/HR/DeiSnapshotService.php
app/Actions/HR/{SubmitOwnDeiAttributesAction,WithdrawDeiConsentAction}.php
app/Console/Commands/HR/GenerateDeiSnapshotsCommand.php
app/Filament/HR/Pages/DeiDashboardPage.php
database/factories/HR/DeiAttributeFactory.php
tests/Feature/HR/{DeiPrivacyTest,DeiSnapshotTest}.php
```

---

## Related

- [[domains/hr/hr-analytics]]
- [[domains/core/data-privacy]]
- [[architecture/patterns/encryption]]
- [[architecture/data-lifecycle]]
