---
domain: legal
module: legal-contracts
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Contracts — Security

## Access contract

Every artifact gates on `canAccess() = Auth::user()->can('legal.contracts.view-any') && BillingService::hasModule('legal.contracts')` per [[../../../architecture/filament-patterns]] #1.

## Permissions

`legal.contracts.view-any` · `legal.contracts.create` · `legal.contracts.update` · `legal.contracts.sign-off` · `legal.contracts.terminate`

## Upload hardening (medium — per [[../../../build/security-audit-2026-06-11]])

- Signed-contract Media Library collection: **PDF-only** whitelist, max size cap, `companies/{id}/`-scoped storage path.

## Data ownership

Writes only `legal_contracts`, `legal_contract_obligations`. Counterparty (crm/ops) and matter links are read-only references — never direct writes into other domains' tables ([[../../../security/data-ownership]]).
