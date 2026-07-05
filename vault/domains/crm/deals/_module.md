---
domain: crm
module: deals
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-05
---

# Deals

Deal records with value, stage, probability, close date, products/services, and owner. The core revenue tracking object in CRM — everything in the sales motion (pipeline board, quotes, forecasting, invoice creation) hangs off this record.

> This module is planned for build. All prior "shipped/built" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

---

## Module-key

`crm.deals`

**Priority:** v1-core  
**Panel:** crm  
**Permission prefix:** `crm.deals`  
**Tables:** `crm_deals`, `crm_deal_contacts`, `crm_deal_products`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | Deals attach to contacts/accounts |
| Hard | [[../../crm/pipeline/_module\|crm.pipeline]] | Owns `crm_pipeline_stages`; deals live in stages |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../../finance/invoicing/_module\|finance.invoicing]] | `DealWon` → draft invoice stub; without it the event fires unconsumed and `CreateInvoiceAction` is hidden |
| Soft | [[../../crm/quotes/_module\|crm.quotes]] | Accepted quote pre-fills deal products; degrades to manual line items |
| Soft | crm.pricing (price books) | Product line items link to catalog; degrades to free-text lines |
| Soft | [[../../crm/activities/_module\|crm.activities]] | Activity timeline tab; tab hidden without it |

---

## Core Features

- Deal record: name, value, stage, probability, expected close date, owner, contact(s), account
- Custom pipeline stages per company (e.g. Lead → Qualified → Proposal → Won | Lost)
- Stage transitions via `spatie/laravel-model-states`
- Won/lost tracking: reason, competitor, lost-to
- Products/line items on deal: link to product catalog (if CRM Pricing module active)
- Deal age: days since last activity, days in current stage
- Deal duplication: copy deal to start a new cycle with same contact
- Invoice creation: one-click create Finance invoice from a won deal
- Activity timeline on deal: calls, emails, meetings

See [[./features/won-lost-flow|Won/Lost Flow feature]] and [[./features/invoice-creation|Invoice Creation feature]] for deeper notes.

---

## Build Manifest

```
database/migrations/xxxx_create_crm_deals_table.php
database/migrations/xxxx_create_crm_deal_contacts_table.php
database/migrations/xxxx_create_crm_deal_products_table.php
app/Models/CRM/{Deal,DealContact,DealProduct}.php
app/States/CRM/Deal/{DealState,Open,Won,Lost}.php
app/Data/CRM/{CreateDealData,UpdateDealData,CloseDealData,DealData}.php
app/Contracts/CRM/DealServiceInterface.php
app/Services/CRM/DealService.php
app/Exceptions/CRM/{ClosedDealImmutableException}.php
app/Events/CRM/{DealWon,DealLost}.php
app/Filament/CRM/Resources/DealResource.php (+ Pages: List, Create, View, Edit)
database/factories/CRM/{DealFactory,DealProductFactory}.php
tests/Feature/CRM/{DealTest,DealCloseTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see/move/close company B deals
- [ ] Module gating: resource hidden when `crm.deals` inactive
- [ ] Close as won fires `DealWon` with contract payload (value, currency, ids)
- [ ] Close as lost requires `lost_reason`; fires `DealLost`
- [ ] Closed deal cannot move stage (`ClosedDealImmutableException`)
- [ ] Stage move resets `stage_entered_at` + applies stage default probability
- [ ] `CreateInvoiceAction` hidden when finance.invoicing inactive
- [ ] Weighted pipeline value computed via brick/money (no float math)
- [ ] Duplicate copies contacts + products, resets status/stage

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `DealWon` | finance.invoicing, crm.contacts, crm.forecasting | Draft invoice stub; lifecycle → customer; forecast update |
| Fires | `DealLost` | crm.forecasting, crm.sequences | Forecast update; trigger nurture/win-back sequence |
| Fires | `DealStageChanged` | crm.pipeline (board) | Broadcast via pipeline board (ShouldBroadcast) |
| Consumes | `QuoteAccepted` | crm.quotes (within-domain) | Pre-fill deal products; degrades to manual line items |
| Reads | `ContactService` | crm.contacts | Attach contacts/accounts to a deal |
| Reads | `crm_pipeline_stages` | crm.pipeline | Deals live in stages; move via `DealService::moveToStage` |

**Data ownership:** `crm.deals` writes only `crm_deals`, `crm_deal_contacts`, `crm_deal_products`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

---

## Related

- [[../../crm/pipeline/_module|crm.pipeline]]
- [[../../crm/contacts/_module|crm.contacts]]
- [[../../crm/quotes/_module|crm.quotes]]
- [[../../crm/activities/_module|crm.activities]]
- [[../../finance/invoicing/_module|finance.invoicing]]
- [[../../../architecture/event-bus]]
- [[../../../architecture/ui-strategy]]
