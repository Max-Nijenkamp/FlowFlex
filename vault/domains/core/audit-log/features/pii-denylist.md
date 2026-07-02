---
domain: core
module: audit-log
feature: pii-denylist
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: PII Denylist

Parent: [[../_module]] · See [[../security]] · [[../architecture]]

The audit log records **what changed**, not sensitive raw values. `properties` may carry before/after data for ordinary fields but must never carry raw values of encrypted/PII fields (national ID, DOB, IBAN, salary, etc.) — field **names** only.

- `AuditLogger::log` strips PII keys against a denylist before persisting *(assumed: a per-model `$auditExclude` list drives which keys are stripped)*.
- Covered by the `AuditPiiTest` (see Build Manifest).
- Aligns with the retention + privacy rules in [[../../../architecture/data-lifecycle]] and [[../../../security/encryption]].

## UI

- **Kind**: background
- **Page**: background (no page) — the denylist runs inside `AuditLogger::log()` at write time; it has no screen. Its effect is visible in the [[log-browser]] where PII-keyed values never appear.
- **Layout**: n/a. Trigger: every call to `AuditLogger::log(event, subject, causer, properties)` passes `properties` through the denylist strip before persisting to `activity_log`.
- **Key interactions**: none directly — it is an always-on filter on the audit write path.
- **States**: empty = no properties to strip (row written as-is) · loading = n/a · error = denylist misconfig would risk leaking PII *(assumed guard: fail-closed / test-covered)* · selected = n/a.
- **Gating**: no user gate — it is enforced code-path-wide; the surrounding log write inherits the caller's context.

## Data

- Owns / writes: `activity_log` (this module's only table) — writes the sanitised `properties` (PII field names only, no raw values).
- Reads: the per-model denylist / `$auditExclude` definition *(assumed)* to know which keys to strip; reads `CompanyContext` for `company_id`.
- Cross-domain writes: none — the denylist only shapes what audit-log writes into its own table ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — invoked in-process by every domain's writes via `AuditLogger::log`.
- Feeds: none.
- Shared entity: the list of encrypted/PII fields is conceptually owned by each source domain's model (its `encrypted` casts); the denylist mirrors those field names *(assumed per-model `$auditExclude`)*.
