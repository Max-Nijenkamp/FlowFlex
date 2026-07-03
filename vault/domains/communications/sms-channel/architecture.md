---
domain: communications
module: sms-channel
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# SMS Channel — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `SmsDriver implements ChannelDriverInterface` | `send(SendMessageData): void` | Throws `RecipientOptedOutException` if the number is opted out; estimates segments; records cost; sends via provider. Registered as the `sms` driver. |
| `SmsWebhookController` | public (guest) endpoint | Signature-verified. Inbound `STOP` → opt-out row + confirmation *(assumed: provider handles confirmation)*; normal inbound → `InboundMessageData` → inbox; status callbacks update `comms_messages.delivery_status`. |
| `OptOutService::isOptedOut` | `isOptedOut(string $e164): bool` | Checked by the driver **and** by broadcast recipient materialisation. |

**Driver rule:** the driver enforces opt-out + records cost, but never writes `comms_messages` — the inbox writes the row from normalised data.

## Events

None fired or consumed. Cross-domain effect is via the inbox driver contract + `OptOutService` read API, not events. See [[../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SmsChannelResource` | #1 CRUD resource | tweaks: custom-header-actions (test-send) | Connect provider; credentials write-only; test send |
| Opt-out list | #1 CRUD resource | tweaks: read-only-flow-owned (writes owned by `OptOutService` / STOP webhook) | Compliance view of opted-out numbers |

Sending happens through the [[../shared-inbox/_module|Shared Inbox]] composer (segment counter shown).

**Access contract (mandatory):** both artifacts gate on
`canAccess() = Auth::user()->can('comms.sms.view-any') && BillingService::hasModule('comms.sms')`
per [[../../../architecture/filament-patterns]] #1. Provider connect / test-send additionally require
`comms.sms.manage` ([[./security]]). The opt-out list is read-only — opt-outs are written by the STOP webhook via
`OptOutService`. Sending is gated by the inbox (`comms.inbox.reply`); the webhook (`SmsWebhookController`) is a
signed guest endpoint, not a Filament artifact.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| SMS-config CRUD (connect, credentials) | Optimistic | single row per company; `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Opt-out write (STOP inbound) | n/a | Append-only upsert into `comms_sms_optouts`, `phone_e164` unique per company — idempotent, no in-place update to race |
| Message send + cost record | n/a | Append-only via `InboxService`; the inbox owns the `comms_messages` row (cost rides in `meta`) + `external_id` dedupe |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| Inbound/status webhook processing | default | per webhook | `external_id` dedupe (inbox) |

See [[../../../architecture/queue-jobs]].

## Implementation Notes (tense-softened)

- The driver is designed to **block opted-out numbers everywhere** — a single `OptOutService` check is shared by the driver and broadcast materialisation.
- The webhook is designed to be **signature-verified** and to treat a `STOP` inbound as an opt-out event rather than a normal message.
- Cost is designed to be recorded from provider callbacks into `comms_messages.meta.cost_cents` *(assumed meta column)* using `brick/money`.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../broadcast/_module|Broadcast]]
