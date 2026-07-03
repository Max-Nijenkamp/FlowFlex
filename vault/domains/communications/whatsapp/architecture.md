---
domain: communications
module: whatsapp
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# WhatsApp — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `WhatsAppDriver implements ChannelDriverInterface` | `send(...): void` | Checks the 24h customer-service window (last inbound timestamp); free-form send inside the window, outside → `TemplateRequiredException`. Handles media send. Registered with the inbox as the `whatsapp` channel driver. |
| `WhatsAppWebhookController` | public (guest) endpoint | Verify-token / signature validated **before** any processing; normalises the provider payload → `InboundMessageData` → hands to `InboxService`; delivery/read receipts update `comms_messages.delivery_status`. |
| `SubmitTemplateAction` | `run(WhatsAppTemplate): void` | Submits a `draft` template to the provider for approval; sets status to `pending`, records `external_template_id`. |
| `SyncTemplateStatusJob` | queued job | Polls the provider for approval status; upserts by `external_template_id` (`pending → approved / rejected`). |
| `ConnectWhatsAppAction` | `run(config): void` | Verifies credentials with the provider **before** persisting `comms_whatsapp_config` (encrypts `api_key` + `webhook_secret`). |

**Driver rule:** `WhatsAppDriver` owns the send-time policy (window check, template requirement, media). It never writes `comms_messages` — it returns/normalises so the inbox writes the row.

## Events

None fired or consumed. See [[../../../architecture/event-bus]] for the platform contract; this module defines no cross-domain events. Cross-domain effect happens through the inbox `ChannelDriver` contract, not events.

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `WhatsAppTemplateResource` | #1 CRUD resource | tweaks: state-badge-column (approval status), custom-header-actions (submit-for-approval) | Create + submit templates; `{{n}}` placeholders; rejected shows reason |
| `WhatsAppConfigPage` | #7 wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] | Connect number, enter credentials (write-only display); `ConnectWhatsAppAction` verifies with the provider before save |

Sending happens through the [[../shared-inbox/_module|Shared Inbox]] composer (template picker appears outside the 24h window).

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('comms.whatsapp.view-any') && BillingService::hasModule('comms.whatsapp')`
per [[../../../architecture/filament-patterns]] #1. `WhatsAppConfigPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. Config writes additionally require `comms.whatsapp.manage-config`,
template writes `comms.whatsapp.manage-templates` ([[./security]]). The webhook (`WhatsAppWebhookController`) is a
signed guest endpoint, not a Filament artifact.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Template CRUD (draft edit) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Config connect / credential update | Optimistic | single row per company; `updated_at` stale-check after provider verify ([[../../../architecture/patterns/optimistic-locking]]) |
| Template status sync (`SyncTemplateStatusJob`) | n/a | Provider-driven upsert by `external_template_id` — no concurrent user write to race |
| Message send / inbound receipt | n/a | Append-only via `InboxService`; the inbox owns the `comms_messages` write + `external_id` dedupe |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| `SyncTemplateStatusJob` | default | hourly | upsert by `external_template_id` |
| Webhook inbound processing | default | per webhook | `external_id` dedupe (done by inbox) |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None specified here. Inbound messages surface in the shared inbox, which owns any realtime/broadcast behaviour for conversations.

## Implementation Notes (tense-softened)

- The driver is designed to read the **last inbound timestamp** to decide whether a free-form send is allowed; outside the 24h window it raises `TemplateRequiredException` so the composer switches to the template picker.
- The webhook controller is designed to **fail closed**: a bad verify-token/signature returns `403` and stores nothing.
- Credentials are designed to be **verified with the provider before save**, so a broken connection never persists.
- Template status is designed to be **provider-driven**: local rows mirror provider approval, synced hourly and upserted by `external_template_id`.
