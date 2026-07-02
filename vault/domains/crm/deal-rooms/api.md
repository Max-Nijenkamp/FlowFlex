---
domain: crm
module: deal-rooms
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deal Rooms — API & DTOs

## Input DTOs

### CreateDealRoomData

| Field | Type | Rules |
|---|---|---|
| deal_id | ulid | Required; no existing room for the deal. |
| expires_at | timestamp | Must be in the future. |

Buyer-side updates (public, token-authed) are limited to toggling an action-item status and logging a document view — no DTO-validated writes beyond these *(assumed: buyers cannot upload in v1)*.

## Output DTOs

### DealRoomData

Internal projection for the Filament resource — deal, token, branding, expiry, documents, action items, stakeholders, engagement stats.

### DealRoomPublicData

Buyer-facing projection returned by `publicView(token)` — branded shell, shared documents, action plan, stakeholders. Never exposes internal CRM data beyond shared content.

## Public / Portal Endpoints

| Method | Route | Controller | Notes |
|---|---|---|---|
| GET | `/room/{token}` | `PublicDealRoomController` | Guest guard; renders the Vue + Inertia room. Rate-limited. |
| POST | `/room/{token}/documents/{id}/view` | `PublicDealRoomController` | Logs a view, returns a signed temp URL. |
| PATCH | `/room/{token}/action-items/{id}` | `PublicDealRoomController` | Toggles a buyer-side action item. |

See [[../../../architecture/patterns/dto-pattern]] and [[../../../architecture/ui-strategy]].
