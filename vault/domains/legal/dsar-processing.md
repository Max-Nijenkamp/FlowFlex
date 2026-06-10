---
type: module
domain: Legal & Compliance
domain-key: legal
panel: legal
module-key: legal.dsar
status: planned
priority: p3
depends-on: [core.privacy, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: [DSARRequestSubmitted]
patterns: [gdpr, events]
tables: [legal_dsar_actions]
permission-prefix: legal.dsar
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# DSAR Processing (Legal layer)

Legal workflow layer over [[domains/core/data-privacy|core.privacy]] DSARs: identity verification, per-domain action tracking, rectification requests, and rejection documentation. **The DSAR record + erasure/export engine live in core.privacy** — this module deepens the process, it does not duplicate it. *(v2 design: v1 spec's separate `legal_dsar_requests` table dropped — works on `dsar_requests` directly; the v1 `DSARErasureRequested` event dropped — erasure runs via core.privacy's PersonalDataRegistry jobs* *(assumed))*

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/data-privacy\|core.privacy]] | DSAR records, export/erasure jobs, PersonalDataRegistry |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Consumes `DSARRequestSubmitted` → creates legal review task on the request
- Identity verification step before processing (verification checklist + method recorded)
- Request types extended: rectification + portability handled as documented manual workflows on top of access/erasure *(assumed)*
- Data discovery view: registry-driven table list of where the subject appears (read from `PersonalDataRegistry`)
- Fulfilment: delegates to core.privacy jobs (access export / erasure cascade)
- Per-domain action log: every DSAR step recorded (`legal_dsar_actions`)
- Rejection with documented reason (e.g. legal hold exemption)
- Deadline view (30-day, from core.privacy `due_at`)

---

## Data Model

### legal_dsar_actions

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| dsar_request_id | ulid FK dsar_requests | core.privacy table |
| action | string | verified / discovery-run / export-delivered / erasure-run / rectified / rejected |
| domain | string nullable | per-domain steps |
| notes | text nullable | required for rejected/rectified |
| performed_by | ulid FK users | |
| performed_at | timestamp | |

Append-only — compliance proof, never purged ([[architecture/data-lifecycle]]).

---

## DTOs

### RecordDsarActionData — dsar_request_id, action (in set), domain?, notes (required_if rejected,rectified)
### VerifyIdentityData — dsar_request_id, method (in:email-challenge,document,in-person *(assumed)*), notes

## Services & Actions

- `LegalDsarService::verify(VerifyIdentityData)` — gate: core.privacy processing blocked until verified when this module active *(hook)*
- `LegalDsarService::discovery(string $requestId): array` — PersonalDataRegistry tables for subject email
- `RecordDsarActionAction`
- Listener `CreateLegalReviewListener` on `DSARRequestSubmitted` (queued, WithCompanyContext)

---

## Filament

**Nav group:** Compliance

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DsarRequestResource` (extended) | #1 CRUD resource | deadline-sorted, verification + action trail |
| `DsarFulfilmentPage` | #7 custom page | discovery table + trigger export/erasure (delegated) |

---

## Permissions

`legal.dsar.process` · `legal.dsar.verify` · `legal.dsar.reject`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] `DSARRequestSubmitted` creates review task/action
- [ ] Processing blocked until verified (gate hook)
- [ ] Discovery lists registry tables for subject
- [ ] Rejection requires notes; action log append-only
- [ ] Fulfilment delegates to core.privacy jobs (no duplicate erasure logic)

---

## Build Manifest

```
database/migrations/xxxx_create_legal_dsar_actions_table.php
app/Models/Legal/DsarAction.php
app/Data/Legal/{RecordDsarActionData,VerifyIdentityData}.php
app/Services/Legal/LegalDsarService.php
app/Actions/Legal/RecordDsarActionAction.php
app/Listeners/Legal/CreateLegalReviewListener.php
app/Filament/Legal/Pages/DsarFulfilmentPage.php
tests/Feature/Legal/LegalDsarTest.php
```

---

## Related

- [[domains/core/data-privacy]]
- [[architecture/data-lifecycle]]
- [[architecture/event-bus]]
