---
domain: crm
module: deal-rooms
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deal Rooms — Architecture

## State Machine

None. Rooms have a lifecycle expressed via `expires_at` / `revoked_at` timestamps, not a formal state machine.

## Services & Actions

| Method | Signature | Purpose |
|---|---|---|
| `DealRoomService::create` | `(CreateDealRoomData): DealRoomData` | Creates a room for a deal (no existing room). |
| `DealRoomService::publicView` | `(token): DealRoomPublicData` | Resolves a valid, unexpired, unrevoked token; never exposes internal CRM data beyond shared content. |
| `TrackDocumentViewAction::run` | `(token, documentId): void` | Increments `view_count`, returns a signed temp URL. |
| `RevokeRoomAction::run` | `(roomId): void` | Revokes room access. |

## Events

None.

## Filament Artifacts

Nav group: **Activities**.

| # | Artifact | ui-strategy row | Notes |
|---|---|---|---|
| 1 | `DealRoomResource` | CRUD resource | Per-deal room, engagement panel, revoke action. |
| 16 | Public room `/room/{token}` | Vue + Inertia | Branded; action plan, docs, stakeholders. |

**Access contract** (Filament): `canAccess()` = `can('crm.deal-rooms.view-any') && hasModule('crm.deal-rooms')`. See [[../../../architecture/filament-patterns]].

The public room at `/room/{token}` is a Vue + Inertia page (ui-strategy row #16 — public/portal external access) served by `PublicDealRoomController`, not a Filament panel. See [[../../../architecture/ui-strategy]].

## Jobs & Scheduling

None.

## Caching

None.

## Search & Realtime

None. Engagement tracking is write-on-view, read in the Filament engagement panel.
