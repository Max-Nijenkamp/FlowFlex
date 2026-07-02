---
domain: core
module: data-privacy
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Data Privacy

GDPR tooling: DSAR (Data Subject Access Request) management, consent logs, full-dataset export, and an erasure queue. Ensures compliance from day one — implements the policy in [[../../../architecture/data-lifecycle]] via a `PersonalDataRegistry` that every module registers its PII tables into (drives both export scope and erasure cascade).

- **module-key:** `core.privacy` · **panel:** app · **priority:** v1
- **fires-events:** `DSARRequestSubmitted`

## Sibling notes

- [[architecture]] — registry, jobs, state machine, flow diagram
- [[data-model]] — `dsar_requests`, `consent_logs`, ERD, state table
- [[api]] — `CreateDsarRequestData`, `DSARRequestSubmitted`, `PersonalDataRegistry` contract
- [[security]] — permissions, export rate limiter, legal-hold, tenancy
- [[unknowns]] — UNVERIFIED Build-Manifest items + `*(assumed)*` markers
- Features: [[features/dsar-queue]] · [[features/data-export]] · [[features/erasure-cascade]] · [[features/consent-log]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../company-settings/_module]] (core.settings) | retention config, DSAR contact email |
| Hard | foundation.queues | export + erasure jobs |
| Hard | [[../file-storage/_module]] (core.files) | export ZIP storage |
| Hard | core.rbac + [[../billing-engine/_module]] (core.billing) | permissions + module gating |

## Core Features

- DSAR queue: log and process data access + erasure requests from employees or customers
- Data export: full company dataset export as ZIP (CSV per model type) — owner-available
- Erasure workflow: soft-delete → anonymise → schedule hard delete; cascade rules per table family from [[../../../architecture/data-lifecycle]] (legal holds: financial records kept)
- `PersonalDataRegistry`: modules register their PII tables/fields — drives export scope and erasure cascade
- Consent log: track when consent was given/withdrawn per data category per user
- DSAR contact email configurable in Company Settings
- Retention policy: configurable retention period per data type (only lengthening beyond statutory minimums)
- DSAR response deadline tracker: 30-day countdown per request

## Test Checklist

- [ ] Tenant isolation: DSAR of company A invisible to company B
- [ ] Module gating + permission gating on both surfaces
- [ ] Access request produces ZIP containing rows for the subject across registered tables only
- [ ] Erasure: `hr_employees` anonymised per rule, invoices untouched, emergency contacts hard-deleted
- [ ] Erasure with open legal hold (employment ongoing) → rejected path
- [ ] DSAR rows survive (status updated) — compliance proof retained
- [ ] Deadline reminders fire at -7d / -1d once each

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_dsar_requests_table.php
database/migrations/xxxx_create_consent_logs_table.php
app/Models/{DsarRequest,ConsentLog}.php
app/States/DsarRequest/{DsarRequestState,Received,InProgress,Completed,Rejected}.php
app/Data/CreateDsarRequestData.php
app/Support/Privacy/PersonalDataRegistry.php
app/Jobs/{ProcessAccessRequestJob,ProcessErasureRequestJob}.php
app/Actions/ExportCompanyDataAction.php
app/Events/DSARRequestSubmitted.php
app/Console/Commands/DsarDeadlineReminderCommand.php
app/Filament/App/Resources/DsarRequestResource.php
app/Filament/App/Pages/DataExportPage.php
database/factories/DsarRequestFactory.php
tests/Feature/Core/{DsarAccessTest,DsarErasureTest,CompanyPurgeTest}.php
```

Spec listed `app/.../Core/...`; real layout is flat — corrected above. The spec manifest also listed `PurgeCancelledCompaniesCommand`, which was NOT built — see [[unknowns]].

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | `DSARRequestSubmitted` | notifications (now), legal (P3) | on DSAR create — notifications acks/deadline-tracks; legal records the obligation |
| consumes | none | — | consumes no domain events; instead reads the in-memory `PersonalDataRegistry` (every module registers its PII tables) and reads source-domain data read-only for export |

Data ownership: data-privacy owns and writes only `dsar_requests` and `consent_logs`. It **reads many domains read-only** (export scope + erasure scoping via `PersonalDataRegistry::tablesFor`) and effects erasure in other domains **only via events/registered erasers** — each owning domain anonymises its own PII tables; data-privacy never writes another domain's tables ([[../../../security/data-ownership]]). See the erasure UNVERIFIED note in [[features/erasure-cascade]].

## Related

- [[../../../architecture/data-lifecycle]] — the policy this implements
- [[../../../security/data-privacy-gdpr]]
- [[../company-settings/_module]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../architecture/queue-jobs]] · [[../../../glossary]]
