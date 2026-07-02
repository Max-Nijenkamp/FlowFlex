---
domain: legal
module: dsar-processing
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# DSAR Processing (Legal layer)

Legal workflow layer over [[../../core/data-privacy/_module|core.privacy]] DSARs: identity verification, per-domain action tracking, rectification requests, and rejection documentation. **The DSAR record + erasure/export engine live in core.privacy** — this module deepens the process, it does not duplicate it.

> v2 design: the v1 spec's separate `legal_dsar_requests` table was dropped — this works on `dsar_requests` directly; the v1 `DSARErasureRequested` event was dropped — erasure runs via core.privacy's PersonalDataRegistry jobs *(assumed)*.

---

## Module-key

`legal.dsar`

**Priority:** p3
**Panel:** legal
**Permission prefix:** `legal.dsar`
**Tables:** `legal_dsar_actions`
**Encrypted:** `legal_dsar_actions.notes`
**Consumes-events:** `DSARRequestSubmitted`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/data-privacy/_module\|core.privacy]] | DSAR records, export/erasure jobs, PersonalDataRegistry |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |

---

## Core Features

- [[./features/identity-verification|Identity verification]] — verify subject before processing; gates core.privacy fulfilment
- [[./features/data-discovery|Data discovery]] — registry-driven list of where the subject appears
- [[./features/fulfilment-delegation|Fulfilment delegation]] — delegate export/erasure to core.privacy jobs
- [[./features/action-log-rejection|Action log & rejection]] — append-only per-domain action log; documented rejection

Full data model + flow: [[./data-model]] · [[./architecture]].

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] `DSARRequestSubmitted` creates review task/action
- [ ] Processing blocked until verified (gate hook)
- [ ] Discovery lists registry tables for subject
- [ ] Rejection requires notes; action log append-only
- [ ] Fulfilment delegates to core.privacy jobs (no duplicate erasure logic)

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `DSARRequestSubmitted` | core.privacy | `CreateLegalReviewListener` creates a review action on the request |
| Reads | `PersonalDataRegistry` API | core.privacy | data-discovery: tables where the subject appears (read-only) |
| Feeds (delegates) | export/erasure job trigger | core.privacy | fulfilment delegated — legal.dsar never runs erasure itself |

**Data ownership:** `legal.dsar` writes only `legal_dsar_actions` (append-only). The DSAR record (`dsar_requests`) + erasure/export engine are owned by core.privacy — this module reads them and triggers core.privacy's own jobs, never writing privacy tables ([[../../../security/data-ownership]]).

---

## Related

- [[../../core/data-privacy/_module|core.privacy]]
- [[../../../architecture/data-lifecycle]]
- [[../../../architecture/event-bus]]
- [[./decisions]] · [[./unknowns]] · [[./security]] · [[./api]]
