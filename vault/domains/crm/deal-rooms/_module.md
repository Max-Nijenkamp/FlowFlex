---
domain: crm
module: deal-rooms
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# CRM Deal Rooms

A shared digital space for buyer and seller during a deal — documents, a mutual action plan, Q&A, and stakeholder tracking. A modern "digital sales room".

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

`crm.deal-rooms`

**Priority:** v1  
**Panel:** crm  
**Permission prefix:** `crm.deal-rooms`  
**Tables:** `crm_deal_rooms`, `crm_deal_room_documents`, `crm_deal_room_action_items`, `crm_deal_room_stakeholders`

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../../crm/deals/_module\|Deals]] | One room per deal. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating. |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permission enforcement. |
| Hard | core.files | Shared documents via Media Library. |
| Soft | [[../../crm/contacts/_module\|Contacts]] | Stakeholder ↔ contact link. |

## Core Features

- Deal room per deal — shared space accessible by external buyers via a tokenised, expiring link.
- Document sharing — proposals, contracts, case studies, with view tracking.
- Mutual action plan — a shared checklist both sides update.
- Q&A thread — buyer asks, seller answers; v1 uses action items with comments, a dedicated thread is later *(assumed)*.
- Stakeholder map — who's involved buyer-side and their role.
- Engagement tracking — which buyers viewed what, and when.
- Branded room — company logo / colours from settings.
- Expiry / access control on the room link, revocable.

See [[features/tokenised-access]] and [[features/engagement-tracking]] for the flows.

## Build Manifest

```
database/migrations/xxxx_create_crm_deal_rooms_table.php
database/migrations/xxxx_create_crm_deal_room_documents_table.php
database/migrations/xxxx_create_crm_deal_room_action_items_table.php
database/migrations/xxxx_create_crm_deal_room_stakeholders_table.php
app/Models/CRM/{DealRoom,DealRoomDocument,DealRoomActionItem,DealRoomStakeholder}.php
app/Data/CRM/{CreateDealRoomData,DealRoomData,DealRoomPublicData}.php
app/Services/CRM/DealRoomService.php
app/Actions/CRM/{TrackDocumentViewAction,RevokeRoomAction}.php
app/Http/Controllers/PublicDealRoomController.php + resources/js/Pages/DealRoom/Show.vue
app/Filament/CRM/Resources/DealRoomResource.php
database/factories/CRM/DealRoomFactory.php
tests/Feature/CRM/{DealRoomAccessTest,DealRoomEngagementTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see or manage company B's deal rooms
- [ ] Module gating: artifacts hidden when `crm.deal-rooms` inactive
- [ ] Public token resolves only its own room; expired / revoked → 404.
- [ ] Document URL is temp-signed; raw media path never exposed.
- [ ] View tracking increments once per view event.
- [ ] Buyer can toggle buyer-side items only.
- [ ] One room per deal enforced.
- [ ] Public route is rate-limited.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | Deal read API | [[../deals/_module\|crm.deals]] | One room per deal; deal context. |
| Reads | Contact read API | [[../contacts/_module\|crm.contacts]] | Stakeholder ↔ contact link. |
| Reads | Quote read API | [[../quotes/_module\|crm.quotes]] *(assumed)* | Quotes surfaced as room assets. |
| Fires | `DealRoomViewed` / engagement events | [[../revenue-intelligence/_module\|crm.revenue-intelligence]] | Buyer engagement feeds deal health. |
| Reads | core.files (Media Library) | foundation.files | Shared documents via signed temp URLs. |

Token portal (`/room/{token}`) is public (guest guard).

**Data ownership:** `deal-rooms` writes only `crm_deal_rooms`, `crm_deal_room_documents`, `crm_deal_room_action_items`, `crm_deal_room_stakeholders`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../deals/_module|Deals]]
- [[../contacts/_module|Contacts]]
- [[../../../architecture/ui-strategy|UI Strategy]] (tokenised external access)
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
