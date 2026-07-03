---
domain: crm
module: contracts
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# CRM Contracts

Customer contracts with renewal tracking, value, and signature status. A sales-focused contract lifecycle — distinct from Legal's full legal contract management.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

`crm.contracts`

**Priority:** v1  
**Panel:** crm  
**Permission prefix:** `crm.contracts`  
**Tables:** `crm_contracts`

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../../crm/deals/_module\|Deals]] | Contracts are generated from won deals. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating — contracts panel is billable. |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permission enforcement on every resource. |
| Hard | core.files | Signed PDF storage via Media Library. |
| Hard | [[../../../infrastructure/mail\|core.notifications]] | Renewal alerts before expiry. |
| Soft | [[../../crm/quotes/_module\|Quotes]] | Accepted quote pre-fills a contract. |
| Soft | [[../../legal/legal-contracts/_module\|Legal Contracts]] | Deep legal lifecycle (P3). |

## Core Features

- Contract record — customer (account), deal, title, value, start/end dates, renewal terms, status.
- Status machine — draft → sent → signed → active → expired | terminated.
- Generated from a won deal or an accepted quote.
- E-signature — v1 is manual signed-PDF upload plus a signed flag; DocuSign / native e-sign is a later ADR *(assumed)*.
- Renewal tracking — renewal date, auto-renew flag, notice period.
- Renewal alerts before expiry (90 / 30 days *(assumed)*).
- Contract value feeds recurring-revenue tracking.
- Document storage — signed PDF via Media Library.
- Linked to a CRM account.

See [[features/contract-lifecycle]] and [[features/renewal-tracking]] for the flows.

## Build Manifest

```
database/migrations/xxxx_create_crm_contracts_table.php
app/Models/CRM/Contract.php
app/States/CRM/Contract/{ContractState,Draft,Sent,Signed,Active,Expired,Terminated}.php
app/Data/CRM/{CreateContractData,TerminateContractData,ContractData}.php
app/Services/CRM/ContractService.php
app/Console/Commands/CRM/ContractLifecycleCommand.php
app/Filament/CRM/Resources/ContractResource.php
app/Filament/CRM/Pages/ContractRenewalsPage.php
resources/views/filament/crm/pages/contract-renewals-page.blade.php
app/Filament/CRM/Widgets/ContractRenewalWidget.php
database/factories/CRM/ContractFactory.php
tests/Feature/CRM/{ContractLifecycleTest,ContractRenewalTest,ContractRenewalsPageTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see/manage company B contracts
- [ ] Module gating: artifacts hidden when `crm.contracts` inactive
- [ ] Create-from-deal prefills account and value.
- [ ] Sign requires a PDF upload and sets `signed_at`.
- [ ] Lifecycle command handles activation, expiry, auto-renew, and alerts once per level.
- [ ] Terminate requires a reason and is audited.
- [ ] Recurring revenue normalises yearly → monthly correctly (brick/money).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | read query | crm.deals | A won deal spawns a contract (`createFromDeal`) — prefills account + value. Read-only *(assumed trigger)*. |
| Reads | read query | crm.quotes | An accepted quote can prefill a contract. Read-only. |
| Fires | renewal reminder | core.notifications | Pre-expiry alerts (90 / 30 days) via `ContractLifecycleCommand`. |
| Fires | recurring-revenue feed | finance (invoicing) | Contract value → recurring-revenue / invoicing. *(assumed — deferred; may consume nothing cross-domain in v1.)* |
| Consumes | — | — | None confirmed cross-domain. |

**Data ownership:** `crm.contracts` writes only `crm_contracts` (plus its signed-PDF Media Library attachments); all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../deals/_module|Deals]]
- [[../quotes/_module|Quotes]]
- [[../../legal/legal-contracts/_module|Legal Contracts]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
