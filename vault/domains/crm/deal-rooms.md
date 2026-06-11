---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.deal-rooms
status: planned
priority: v1
depends-on: [crm.deals, core.billing, core.rbac, core.files]
soft-depends: [crm.contacts]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [crm_deal_rooms, crm_deal_room_documents, crm_deal_room_action_items, crm_deal_room_stakeholders]
permission-prefix: crm.deal-rooms
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Deal Rooms

Shared digital space for buyer and seller during a deal — documents, mutual action plan, Q&A, and stakeholder tracking. A modern "digital sales room".

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] | one room per deal |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, shared documents |
| Soft | [[domains/crm/contacts\|crm.contacts]] | stakeholder ↔ contact link |

---

## Core Features

- Deal room per deal: shared space accessible by external buyers (tokenised link, expiring)
- Document sharing: proposals, contracts, case studies (view tracking)
- Mutual action plan: shared checklist of steps to close (both sides update)
- Q&A thread: buyer asks, seller answers *(v1: action items with comments; dedicated thread later *(assumed)*)*
- Stakeholder map: who's involved on the buyer side, their role
- Engagement tracking: which buyers viewed what, when
- Branded room (company logo/colours from settings)
- Expiry/access control on the room link; revocable

---

## Data Model

### crm_deal_rooms

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), deal_id FK unique | ulid | one room per deal |
| access_token | uuid unique | public link |
| branding | jsonb | logo/colour overrides |
| expires_at | timestamp | default deal close date + 30d *(assumed)* |
| revoked_at | timestamp nullable | |

### crm_deal_room_documents

| Column | Type | Notes |
|---|---|---|
| id, room_id FK, company_id | ulid | |
| media_id | ulid FK media | tenant-scoped file |
| view_count | int default 0 | |
| last_viewed_at | timestamp nullable | |

### crm_deal_room_action_items

| Column | Type | Notes |
|---|---|---|
| id, room_id FK, company_id | ulid | |
| description | string | |
| owner_side | string | buyer / seller |
| status | string default `open` | open / done |
| due_date | date nullable | |

### crm_deal_room_stakeholders

| Column | Type | Notes |
|---|---|---|
| id, room_id FK, company_id | ulid | |
| name / role | string | |
| contact_id | ulid nullable FK | |

---

## DTOs

### CreateDealRoomData — deal_id (no existing room), expires_at (future)
### Buyer-side updates (public, token-authed): toggle action item status, log document view — no DTO-validated writes beyond these *(assumed: buyers cannot upload v1)*

## Services & Actions

- `DealRoomService::create(CreateDealRoomData $data): DealRoomData`
- `DealRoomService::publicView(string $token): DealRoomPublicData` — valid token (unexpired, unrevoked); never exposes internal CRM data beyond shared content
- `TrackDocumentViewAction::run(string $token, string $documentId): void` — increments + signed temp URL
- `RevokeRoomAction::run(string $roomId): void`

---

## Filament

**Nav group:** Activities

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DealRoomResource` | #1 CRUD resource | per-deal room, engagement panel, revoke action |
| Public room | Vue + Inertia `/room/{token}` (ui-strategy row #16) | branded, action plan, docs, stakeholders |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.deal-rooms.view-any') && BillingService::hasModule('crm.deal-rooms')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Public/portal guard** (HIGH): Specify the public route guard (guest/no app-session) and confirm the token resolves the company context without exposing the authenticated app guard; document middleware for /room/{token}.

---

## Permissions

`crm.deal-rooms.view-any` · `crm.deal-rooms.create` · `crm.deal-rooms.update` · `crm.deal-rooms.revoke`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Public token resolves only its room; expired/revoked → 404
- [ ] Document URL is temp-signed; raw media path never exposed
- [ ] View tracking increments once per view event
- [ ] Buyer can toggle buyer-side items only
- [ ] One room per deal enforced
- [ ] Public route rate-limited

---

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

---

## Related

- [[domains/crm/deals]]
- [[architecture/security]] — tokenised external access
- [[frontend/_index]]
