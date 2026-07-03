---
domain: communications
module: email-channel
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Email Channel — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `EmailChannelDriver implements ChannelDriverInterface` | `send(SendMessageData): void` | Send via Resend with `from` = channel address; inject the channel signature; set `References`/`In-Reply-To` threading headers. Registered with the inbox as the `email` driver. |
| `InboundCommsEmailController` | public (guest) endpoint | Signature-verified provider webhook; resolves the channel by `inbound_token`; drops spam-scored mail; parses HTML/plain → `InboundMessageData` → `InboxService::handleInbound`. |
| Threading resolver | (in driver/controller) | Maps `References`/`In-Reply-To` message-ids → conversation; fallback `(channel, from-address)` open conversation. |

**Driver rule:** the driver never writes `comms_messages` — it hands normalised inbound to the inbox, and for outbound it sends then lets the inbox record the row.

## Events

None fired or consumed. Cross-domain effect is via the inbox `ChannelDriver` contract, not events. See [[../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `EmailChannelResource` | #1 CRUD resource | tweaks: custom-header-actions (test-connection) | Shows the forwarding address to configure; signature editor (purified HTML); the message composer itself lives in the shared inbox |

**Access contract (mandatory):** `EmailChannelResource` gates on
`canAccess() = Auth::user()->can('comms.email.view-any') && BillingService::hasModule('comms.email')`
per [[../../../architecture/filament-patterns]] #1. Write/configure actions additionally require `comms.email.manage`
([[./security]]). Sending replies is gated by the inbox (`comms.inbox.reply`); this module owns no message write.
The inbound webhook (`InboundCommsEmailController`) is a signed guest endpoint, not a Filament artifact — see [[./security]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Email-channel CRUD (connect, signature, config) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Inbound parse → message write | n/a | Append-only via `InboxService::handleInbound`; the inbox owns the row + its `external_id` dedupe |
| Outbound send | n/a | Driver dispatches; the inbox records the message row — no in-place update in this module |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| Inbound parse job | default | per inbound webhook | `external_id` (Message-ID) dedupe done by inbox |

See [[../../../architecture/queue-jobs]] and [[../../../architecture/email]].

## Implementation Notes (tense-softened)

- v1 connection is designed around **forwarding** to a unique inbound address (`{token}@inbound.flowflex.io`) *(assumed)*; OAuth (Gmail/Outlook) is deferred to v1.x.
- The webhook is designed to **fail closed** on a bad signature and to **drop** mail over the spam-score threshold, logging the drop.
- Threading is designed to prefer header matching (`References`/`In-Reply-To`) with a subject + from-address fallback.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/email]]
