---
domain: crm
module: deal-rooms
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Activities

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DealRoomResource` | #1 CRUD resource | tweaks: custom-header-actions (revoke), relation-manager-timeline (engagement panel) | per-deal room; per-document view-count + last-viewed for the seller |
| Public room `/room/{token}` | #16 public/portal (Vue + Inertia) | scoped-portal guard + single-use signed, expiring, revocable token | branded; action plan, docs, stakeholders; served by `PublicDealRoomController` |

**Access contract (mandatory):** every Filament artifact gates on
`canAccess() = Auth::user()->can('crm.deal-rooms.view-any') && BillingService::hasModule('crm.deal-rooms')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not auto-gate them. The public room at `/room/{token}` is a Vue+Inertia surface (ui-strategy row #16, not a Filament artifact) on a guest/scoped-portal guard: it resolves company context from a single-use signed, expiring, revocable `access_token` — never the authenticated app guard — and delivers documents via signed temp URLs. See [[../../../architecture/ui-strategy]] and [[./security]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Room CRUD (content, action plan, stakeholders) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Action-item toggle (seller-side + buyer-side) | Optimistic | `updated_at` stale-check on the item ([[../../../architecture/patterns/optimistic-locking]]) |
| Revoke room (`RevokeRoomAction`) | Optimistic | single `revoked_at` stamp; idempotent (already-revoked is a no-op) |
| Document view tracking (`TrackDocumentViewAction`) | n-a | append-only engagement increment (`view_count`, `last_viewed_at`) via atomic increment — no competing edit |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Caching

None.

## Search & Realtime

None. Engagement tracking is write-on-view, read in the Filament engagement panel.
