---
domain: legal
module: dsar-processing
feature: data-discovery
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Data Discovery

Registry-driven view of every table where the data subject appears, sourced from core.privacy's PersonalDataRegistry.

## Behaviour

- `LegalDsarService::discovery(requestId)` reads `PersonalDataRegistry` for the subject email → list of tables/domains holding their data.
- Read-only: this module does not scan or own those tables; it presents the registry result.
- A `discovery-run` action is appended for the audit trail.

## UI

- **Kind**: custom-page — a discovery table within the fulfilment workflow.
- **Page**: discovery section on `DsarFulfilmentPage` (`/legal/dsar/{id}`).
- **Layout**: table of domains/tables where the subject appears (domain, table, row count/summary); "run discovery" button; results feed the fulfilment step.
- **Key interactions**: run discovery → registry query → populate table → log `discovery-run`.
- **States**: empty ("Run discovery to locate the subject's data") · loading (querying registry) · error (registry unavailable → retry) · selected (a domain expanded).
- **Gating**: `legal.dsar.process`.

## Data

- Owns / writes: `legal_dsar_actions` (`discovery-run`).
- Reads: `PersonalDataRegistry` (core.privacy), read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing directly (works on an existing request).
- Feeds: discovery result scopes the [[./fulfilment-delegation|fulfilment]] step.
- Shared entity: `PersonalDataRegistry` (owned by core.privacy).

## Test Checklist

### Unit
- [ ] Discovery result maps registry entries to domain/table rows for the subject email

### Feature (Pest)
- [ ] `discovery(requestId)` returns only registry tables holding the subject + appends `discovery-run` action
- [ ] Discovery never writes any scanned table (read-only assertion)

### Livewire
- [ ] Run-discovery populates the table; registry-unavailable shows error state with retry
- [ ] Denied without `legal.dsar.process`

## Unknowns

- Registry coverage completeness depends on core.privacy — see core.privacy spec.

## Related

- [[../_module|DSAR Processing]] · [[./identity-verification]] · [[./fulfilment-delegation]]
