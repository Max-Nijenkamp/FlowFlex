---
domain: legal
module: legal-contracts
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Legal Contracts — Security

## Access contract

Every artifact gates on `canAccess() = Auth::user()->can('legal.contracts.view-any') && BillingService::hasModule('legal.contracts')` per [[../../../architecture/filament-patterns]] #1.

## Permissions

| Permission | Grants |
|---|---|
| `legal.contracts.view-any` | List page |
| `legal.contracts.view` | View a single contract |
| `legal.contracts.create` | Create a contract |
| `legal.contracts.update` | Edit a contract; add/edit/close obligations (obligations gate on `.update`) |
| `legal.contracts.delete` | Soft-delete a contract |
| `legal.contracts.sign-off` | Upload signed PDF → `in_review → signed` transition |
| `legal.contracts.renew` | Renew action (resets alert guards, new dates) |
| `legal.contracts.terminate` | Terminate active contract (reason required) |

Verb-per-transition: `sign-off`, `renew`, `terminate` each map to a state-machine transition in
[[./architecture]]; scheduled `active → expired` / `signed → active` are system transitions (no user permission).
Seeded in `PermissionSeeder`.

## Rate Limiting

- The `sign-off` header action attaches a signed PDF (file write) → apply the named `panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].
- Roadmap external signer endpoint (`/sign/{token}`, [[./features/e-signature]]) is a public single-use-token endpoint → must carry a public-token rate limiter and signed-token verification when built.

## Upload hardening (medium — per [[../../../_archive/build-history/security-audit-2026-06-11]])

- Signed-contract Media Library collection: **PDF-only** whitelist, max size cap, `companies/{id}/`-scoped storage path.

## Data ownership

Writes only `legal_contracts`, `legal_contract_obligations`. Counterparty (crm/ops) and matter links are read-only references — never direct writes into other domains' tables ([[../../../security/data-ownership]]).
