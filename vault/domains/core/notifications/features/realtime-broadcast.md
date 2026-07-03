---
domain: core
module: notifications
feature: realtime-broadcast
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Realtime Broadcast

Parent: [[../_module]] · See [[../architecture]]

Live unread-badge updates over Reverb.

- `NotificationCreated` (`ShouldBroadcast`) fires on `company.{id}.notifications` when a notification is created.
- This is the one always-on Reverb broadcast use case in FlowFlex — see [[../../../architecture/websockets]] and [[../../../infrastructure/websockets-reverb]].
- Channel authorization confirms the subscriber belongs to `company_id`, enforcing tenant isolation (see [[../security]]).
- The Filament bell also polls every 30s, so the badge stays fresh even if a socket drops.

## UI

- **Kind**: background
- **Page**: background (no page) — `NotificationCreated` (`ShouldBroadcast`) fires on `company.{id}.notifications`. Its visible effect is the live badge on the [[inbox-bell]] widget.
- **Layout**: none of its own; the client (bell) reacts to the socket event.
- **Key interactions**: unattended — the broadcast fires on notification create; the user only observes the badge updating without a page reload.
- **States**: empty = no live events · loading = socket connecting (bell falls back to 30s poll) · error = socket drop → poll keeps the badge fresh · selected = n/a.
- **Gating**: channel authorization — the subscriber must belong to `company_id` (see [[../security]]); no user-facing permission.

## Data

- Owns / writes: none at broadcast time — the `notifications` row is already written by the create path; this is a signal only.
- Reads: the just-created `notifications` row (own table) to build the payload.
- Cross-domain writes: none — see [[../../../../security/data-ownership]].

## Relations

- Consumes: notification-create signal from this module's own delivery path (fed by domain events via listeners).
- Feeds: `NotificationCreated` on `company.{id}.notifications` — consumed by the client-side [[inbox-bell]] only; **not** a cross-domain server event.
- Shared entity: the Reverb channel `company.{id}.notifications` (tenant-scoped).

## Test Checklist

### Unit
- [ ] `NotificationCreated` payload carries `company_id` and the notification id; channel name resolves to `company.{id}.notifications`

### Feature (Pest)
- [ ] Creating a notification dispatches `NotificationCreated` on the owning company's channel and no other
- [ ] Channel authorization rejects a subscriber whose `company_id` differs from the channel's (tenant isolation)
