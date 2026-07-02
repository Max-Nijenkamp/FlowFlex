---
domain: legal
module: matter-management
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Matter Management

Track legal matters (disputes, cases, advisory work): status, assigned counsel, related documents, deadlines, and spend.

---

## Module-key

`legal.matters`

**Priority:** p3
**Panel:** legal
**Permission prefix:** `legal.matters`
**Tables:** `legal_matters`, `legal_matter_events`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] | gating, permissions, documents |
| Soft | [[../legal-spend/_module\|legal.spend]] | spend summary on matter |
| Soft | [[../legal-contracts/_module\|legal.contracts]], [[../../dms/document-library/_module\|dms.library]] | links |

---

## Core Features

- [[./features/matter-records|Matter records]] — type, status machine, owner, external counsel, priority/risk
- [[./features/matter-timeline|Matter timeline]] — key events + deadlines with 7d alerts
- [[./features/confidential-access|Confidential access]] — second-layer access list on top of CompanyScope

Full data model + state machine: [[./data-model]] · [[./architecture]].

---

## Build Manifest

```
database/migrations/xxxx_create_legal_matters_table.php
database/migrations/xxxx_create_legal_matter_events_table.php
app/Models/Legal/{Matter,MatterEvent}.php
app/States/Legal/Matter/{MatterState,Open,Active,OnHold,Closed}.php
app/Data/Legal/{CreateMatterData,AddMatterEventData}.php
app/Services/Legal/MatterService.php
app/Console/Commands/Legal/MatterDeadlineAlertCommand.php
app/Filament/Legal/Resources/MatterResource.php
database/factories/Legal/{MatterFactory,MatterEventFactory}.php
tests/Feature/Legal/{MatterTest,MatterConfidentialityTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Confidential matter invisible to non-listed users incl. view-any
- [ ] Deadline alert once at 7d
- [ ] Status transitions per machine
- [ ] Spend summary renders when legal.spend active, hidden otherwise

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `LegalSpendService::matterSpend` API | legal.spend | spend summary on matter (soft) |
| Reads | `LegalContractService` / `dms.library` API | legal.contracts, dms | document/contract links (read-only) |

**Data ownership:** `legal.matters` writes only `legal_matters`, `legal_matter_events`; spend + contract + document links are read-only references ([[../../../security/data-ownership]]).

---

## Related

- [[../legal-spend/_module|legal.spend]]
- [[../legal-contracts/_module|legal.contracts]]
- [[../../dms/document-library/_module|dms.library]]
- [[./decisions]] · [[./unknowns]] · [[./security]] · [[./api]]
