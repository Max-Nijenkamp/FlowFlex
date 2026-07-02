---
domain: hr
module: dei-metrics
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# DEI Metrics

Diversity, Equity, and Inclusion metrics and reporting — representation, pay equity, and inclusion trends. Privacy-sensitive: aggregated reporting only, opt-in collection, encrypted at rest. Intended to display **aggregate-only** figures with a suppression threshold; individual self-declared attributes are never rendered anywhere.

> Build status: **planned**. HR code was stripped in [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]; this spec folder is the rebuild blueprint. Nothing here is built, shipped, or tested.

`module-key: hr.dei` · `priority: v1` · panel `hr` · nav group **Analytics**

---

## Purpose

- Collect self-declared diversity attributes (opt-in, consent-logged) — gender, age band, ethnicity (where legally collectable), disability status.
- Produce **aggregated** representation and equity reporting: composition by level/department/role, pay gap by dimension (band-level, never exact salaries), hiring/promotion equity.
- Guarantee anonymity: groups smaller than N are suppressed before storage; dashboards read pre-aggregated snapshots only — never live decrypt-and-group over individuals.

## Intended Behavior

- Employees opt in via HR self-service, declaring attributes with a required consent checkbox; consent is logged via core.privacy.
- Attribute `value` is encrypted at rest (see [[security]]) and never indexed or SQL-filtered.
- A quarterly job decrypts the attribute set inside the job, aggregates, suppresses groups below N, stores a snapshot, and discards individuals (see [[architecture]]).
- Jurisdiction config restricts which dimensions may be collected/reported per company country.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module]] (hr.profiles) | attributes attach to employees |
| Hard | core.privacy ([[../../../security/data-privacy-gdpr]]) | consent tracking is mandatory for collection |
| Hard | core.billing + core.rbac | module gating + permissions |
| Soft | hr.compensation | pay equity (band-level); section hidden without it |
| Soft | hr.recruitment | hiring funnel by dimension; hidden without it |

## Structure

- [[architecture]] — services, actions, snapshot aggregation job, folded Jobs & Scheduling
- [[data-model]] — tables + ERD
- [[api]] — DTOs / services
- [[security]] — **critical**: consent, encryption, aggregate-only, suppression, GDPR erasure
- [[unknowns]] — assumptions and open questions

### Features

- [[features/self-declaration]]
- [[features/dei-attributes-encrypted]]
- [[features/anonymized-snapshots]]
- [[features/dei-dashboard-aggregates]]
- [[features/consent-management]]

### Related

- [[../employee-profiles/_module]]
- [[../../../security/data-privacy-gdpr]]
- [[../../../architecture/patterns/encryption]]
- [[../../../glossary]]

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

## Data Ownership

Owns two tenant-scoped tables: `hr_dei_attributes` (encrypted `value`, individual self-declared attributes — never rendered) and `hr_dei_snapshots` (aggregated, suppressed breakdowns) ([[data-model]]). Reads `hr_employees` (to bind attributes) read-only; writes consent records via core.privacy's API only. Never writes another domain's tables ([[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event / integration | Counterpart | Effect |
|---|---|---|---|
| Writes (via API) | consent log entry | core.privacy | consent + withdrawal logged; `consented_at` references the log |
| Consumes | employee GDPR-erasure signal | core.privacy / `hr.profiles` | hard-delete own `hr_dei_attributes` rows; snapshots retain aggregates only *(assumed wiring)* |
| Reads | employee reference | `hr.profiles` | attach declared attributes to the acting employee |
| Reads | `salary_band` / hiring funnel | `hr.compensation` / `hr.recruitment` | pay-equity + hiring-equity dashboard sections *(assumed)*; hidden without the soft-dep |
| Fires | none outbound | — | privacy — no DEI data leaves the module |


