---
tags: [flowflex, domain/legal, contracts, clm, e-signature, phase/5]
domain: Legal & Compliance
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-06
---

# Contract Management (CLM)

Full contract lifecycle from template to signature to renewal. Never miss a renewal again.

**Who uses it:** Legal team, sales, procurement, HR
**Filament Panel:** `legal`
**Depends on:** [[Document Approvals & E-Sign]]
**Phase:** 5
**Build complexity:** High — 2 resources, 2 pages, 6 tables

## Events Fired

- `ContractExpiring` → consumed by CRM (creates renewal task in [[Sales Pipeline]]), [[Notifications & Alerts]]

## Database Tables (6)

1. `contracts` — contract records with status workflow
2. `contract_versions` — version history per contract
3. `contract_templates` — reusable template library
4. `contract_approvals` — approval step records
5. `contract_signatories` — who must sign, sign status, timestamp
6. `contract_renewal_alerts` — configured renewal alert rules

## Features

- **Full contract lifecycle** — draft → review → approval → signature → active → expired
- **Template library** — reusable contract templates
- **Redlining and version control** — track all changes and who made them
- **Approval chains** — multi-step approval before sending for signature
- **E-signature** — built-in, legally binding (powered by [[Document Approvals & E-Sign]])
- **Auto-renewal alerts** — N days before expiry
- **Contract repository** — full-text search across all contracts
- **Expiry tracking and renewal pipeline**

## Related

- [[Legal Overview]]
- [[Document Approvals & E-Sign]]
- [[Sales Pipeline]]
- [[Quotes & Proposals]]
