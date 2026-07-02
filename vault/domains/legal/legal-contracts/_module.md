---
domain: legal
module: legal-contracts
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Legal Contracts

Central contract repository with key dates, renewal tracking, obligations, and signature status. Different from [[../../crm/contracts/_module|crm.contracts]] (sales-focused): full legal lifecycle, all contract types. The Legal anchor — build first in `/legal`.

> This module is planned for build. All prior "shipped/built" references reflect the pre-strip codebase.

---

## Module-key

`legal.contracts`

**Priority:** p3
**Panel:** legal
**Permission prefix:** `legal.contracts`
**Tables:** `legal_contracts`, `legal_contract_obligations`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, signed PDFs, renewal alerts |
| Soft | [[../../crm/contacts/_module\|crm.contacts]], [[../../operations/suppliers/_module\|operations.suppliers]] | counterparty links |
| Soft | [[../matter-management/_module\|legal.matters]] | contract ↔ matter link |

---

## Core Features

- [[./features/contract-repository|Contract repository]] — record, types, counterparty links, signed-PDF storage
- [[./features/contract-lifecycle|Contract lifecycle]] — state machine + scheduled activation/expiry/renewal + renewal alerts
- [[./features/obligation-tracking|Obligation tracking]] — deliverables + payment milestones with overdue alerts
- [[./features/e-signature|E-signature]] — manual signed-PDF upload v1 (same convention as crm.contracts *(assumed)*)

Full data model + state machine: [[./data-model]] · [[./architecture]].

---

## Build Manifest

```
database/migrations/xxxx_create_legal_contracts_table.php
database/migrations/xxxx_create_legal_contract_obligations_table.php
app/Models/Legal/{LegalContract,ContractObligation}.php
app/States/Legal/LegalContract/{LegalContractState,Draft,InReview,Signed,Active,Expired,Terminated}.php
app/Data/Legal/{CreateLegalContractData,AddObligationData}.php
app/Services/Legal/LegalContractService.php
app/Providers/Legal/LegalServiceProvider.php
app/Console/Commands/Legal/LegalContractLifecycleCommand.php
app/Filament/Legal/Resources/LegalContractResource.php
app/Filament/Legal/Widgets/ContractRenewalWidget.php
database/factories/Legal/{LegalContractFactory,ContractObligationFactory}.php
tests/Feature/Legal/{LegalContractTest,ContractLifecycleTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see/edit/sign/renew company B contracts or obligations
- [ ] Module gating: artifacts hidden when `legal.contracts` inactive
- [ ] Lifecycle command: activate/expire/alerts once per level
- [ ] Notice deadline math (renewal − notice days)
- [ ] Sign requires PDF; terminate requires reason
- [ ] Obligation overdue alert once
- [ ] Renewal resets alert guards

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `ContactService` / `SupplierService` API | crm.contacts, operations.suppliers | counterparty resolution (read-only) |
| Reads | `MatterService` API | legal.matters | contract ↔ matter link (soft) |

**Data ownership:** `legal.contracts` writes only `legal_contracts`, `legal_contract_obligations`; counterparty + matter links are read-only references, never writes into other domains' tables ([[../../../security/data-ownership]]).

---

## Related

- [[../matter-management/_module|legal.matters]]
- [[../../crm/contracts/_module|crm.contracts]]
- [[../../dms/document-library/_module|dms.library]]
- [[./decisions]] · [[./unknowns]] · [[./security]] · [[./api]]
