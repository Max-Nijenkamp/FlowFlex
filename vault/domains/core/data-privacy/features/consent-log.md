---
domain: core
module: data-privacy
feature: consent-log
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Consent Log

Parent: [[../_module]] · See [[../data-model]]

Tracks consent given/withdrawn per data category per user.

- `consent_logs` rows carry `user_id`, `data_category`, `consented_at`, and nullable `withdrawn_at` (null = consent still active).
- Company-scoped via `CompanyScope`.
- Provides the audit trail that backs erasure decisions and DSAR responses.

## UI

- **Kind**: simple-resource
- **Page**: consent-log viewer under `/app` (Settings nav) — a read-mostly `ConsentLog` resource *(assumed: the spec names no dedicated page; consent is written by upstream consent events/flows and viewed here)*
- **Layout**: table of consent records — columns user, data category, consented-at, withdrawn-at (blank = active), with an active/withdrawn status chip; filter by category and by active-only.
- **Key interactions**: staff opens the log to audit who consented to what and when, and whether consent is still active; primarily read/filter (records are typically appended by consent capture flows, not hand-edited).
- **States**: empty = no consent records yet · loading = table load spinner · error = load failure banner · selected = a consent row expanded showing category + timestamps.
- **Gating**: `core.privacy.view-any` (+ `BillingService::hasModule('core.privacy')`). *(assumed — no consent-specific permission is defined in [[../security]])*

## Data

- Owns / writes: `consent_logs` (this module's table) — `user_id`, `data_category`, `consented_at`, nullable `withdrawn_at`; company-scoped via `CompanyScope`.
- Reads: `users` reference (user identity) read-only for display.
- Cross-domain writes: none — consent records live entirely in `consent_logs` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none defined *(assumed — consent give/withdraw could be fed by portal or auth flows; not specified in current notes)*.
- Feeds: none directly — the log is read by the [[erasure-cascade]] and [[dsar-queue]] features as evidence.
- Shared entity: `users` identity is owned elsewhere (platform/auth); read-only here.

## Test Checklist

### Unit
- [ ] Active vs withdrawn derived from `withdrawn_at` null/non-null

### Feature (Pest)
- [ ] Tenant isolation: `consent_logs` company-scoped — company A's records invisible to company B
- [ ] Withdrawal sets `withdrawn_at` once; the record is retained as audit evidence (never hard-deleted)

### Livewire
- [ ] Consent log is read-mostly; filters (category / active-only) narrow the table; `canAccess()` gated on `core.privacy.view-any` + module active
