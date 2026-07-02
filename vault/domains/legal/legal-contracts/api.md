---
domain: legal
module: legal-contracts
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Contracts — Service API

Internal service surface (no public REST v1). Other domains read via these; only this service writes the tables.

## DTOs

- `CreateLegalContractData` — title, counterparty (or link id), type (in set), value_cents?, start/end (end after start), renewal_date?, notice_period_days.
- `AddObligationData` — contract_id, description, due_date, responsible_id.

## Methods

| Method | Purpose | Writes |
|---|---|---|
| `LegalContractService::create(CreateLegalContractData)` | new draft contract | `legal_contracts` |
| `LegalContractService::markSigned(id, pdf)` | in_review → signed, set signed_at | `legal_contracts` |
| `LegalContractService::renew(id, newDates)` | reset alert guards, audited | `legal_contracts` |
| `LegalContractService::terminate(id, reason)` | active → terminated | `legal_contracts` |
| `LegalContractService::addObligation(AddObligationData)` | attach obligation | `legal_contract_obligations` |

## Read surface (consumed by others)

- `legal.matters` reads contract summaries linked by `matter_id` (read-only).

No events fired/consumed in v1 (`fires-events: []`, `consumes-events: []`).
