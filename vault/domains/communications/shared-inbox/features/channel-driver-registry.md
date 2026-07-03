---
domain: communications
module: shared-inbox
feature: channel-driver-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Channel Driver Registry

The extension seam that keeps the inbox channel-agnostic. Each channel module plugs a `ChannelDriver` in; the inbox never touches a provider directly.

## Behaviour

- `ChannelDriverInterface` defines: `send(SendMessageData)`, `normaliseInbound(payload): InboundMessageData`, and `capabilities()` (e.g. supports media, has a send window).
- Channel modules call `ChannelDriverRegistry::register(type, driver)` in their own service providers (`comms.email` → `email`, `comms.whatsapp` → `whatsapp`, `comms.sms` → `sms`).
- On send, `InboxService::send` resolves the conversation's channel type → driver → dispatches. On inbound, the channel webhook normalises then calls `handleInbound`.
- Adding a channel is additive — no inbox code change.

## UI

- **Kind**: background
- **Trigger**: driver registration at boot (service providers). No screen; channel management/config lives in each channel module's own resource/page. `ChannelResource` (#1 CRUD) lists/activates channels.
- **Gating**: `comms.inbox.manage-channels` for channel activation.

## Data

- Owns / writes: `comms_channels` (activation/config non-secret meta) via the inbox.
- Reads: nothing cross-domain.
- Cross-domain writes: none — channel modules write their **own** config/secret tables; the inbox writes only `comms_channels`. Drivers hand normalised data to the inbox, which writes `comms_messages` ([[../../../security/data-ownership]]).

## Relations

- Consumes: driver registrations from `comms.email` / `comms.whatsapp` / `comms.sms`.
- Feeds: the send + inbound pipelines for every channel.
- Shared entity: `comms_channels` (owned here; channel modules FK to it from their config tables).

## Test Checklist

### Unit
- [ ] `ChannelDriverRegistry::register` binds a driver by type; resolving an unregistered type throws
- [ ] `capabilities()` shape validated (supports-media, has-send-window flags)

### Feature (Pest)
- [ ] `InboxService::send` resolves the conversation's channel type to the registered driver and dispatches
- [ ] Send to an inactive channel is rejected
- [ ] Adding a new channel type is additive — registering a driver requires no inbox code change (fake driver test)

## Related

- [[../_module|Shared Inbox]] · [[../architecture]] · [[../../whatsapp/_module]] · [[../../email-channel/_module]] · [[../../sms-channel/_module]]
