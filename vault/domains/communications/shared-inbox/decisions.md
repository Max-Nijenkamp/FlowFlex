---
domain: communications
module: shared-inbox
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shared Inbox — Decisions

## ADR: Channel-agnostic driver registry (source)

- **Context:** Multiple channels (email, WhatsApp, SMS, later social) must feed one inbox.
- **Decision:** The inbox never talks to a provider directly. Each channel module registers a `ChannelDriver` (send, normalise-inbound, capabilities) in its own service provider via `ChannelDriverRegistry::register`. The inbox routes through the resolved driver.
- **Consequences:** New channels are additive — no inbox change. Inbox owns `comms_messages`; drivers hand it normalised `InboundMessageData` (data-ownership boundary preserved).

## ADR: Threading on the open `(channel, external_party)` conversation (source)

- **Decision:** Inbound lands on the existing **open** conversation for a `(channel, external_party)` pair; a new one opens otherwise. Snoozed/resolved threads reopen on inbound.
- **Consequences:** Index `(company_id, channel_id, external_party)` supports the lookup.

## ADR: Idempotent inbound via `external_id` (source)

- **Decision:** `ProcessInboundMessageJob` dedupes on unique `(conversation_id, external_id)`; a re-delivered provider message yields exactly one stored row.

## ADR: No cross-domain domain-events (source)

- **Decision:** `fires-events: []`, `consumes-events: []`. `MessageReceived` is a Reverb **broadcast** event only, not an event-bus domain-event. Cross-domain effects are read-only (CRM contact lookup) or handled by the interested domain's own soft integration (automations).
- **Consequences:** See [[unknowns]] — whether inbound should emit a bus event for CRM activity logging is open.

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/event-bus]]
