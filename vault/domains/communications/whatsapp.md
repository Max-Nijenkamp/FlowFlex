---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.whatsapp
status: planned
priority: p2
depends-on: [comms.inbox, core.billing, core.rbac, foundation.queues]
soft-depends: [comms.broadcast]
fires-events: []
consumes-events: []
patterns: [encryption, queues]
tables: [comms_whatsapp_templates, comms_whatsapp_config]
permission-prefix: comms.whatsapp
encrypted-fields: ["comms_whatsapp_config.api_key", "comms_whatsapp_config.webhook_secret"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# WhatsApp

WhatsApp Business API channel — send and receive WhatsApp messages, manage approved message templates, and broadcast. **The key EU SMB differentiator** (see [[product/positioning]]).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/communications/shared-inbox\|comms.inbox]] | registers the `whatsapp` ChannelDriver; messages flow through comms_messages |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, webhook processing |
| Soft | [[domains/communications/broadcast\|comms.broadcast]] | template broadcasts |

---

## Core Features

- WhatsApp Business API integration via provider (**ADR required at build time: 360dialog vs Twilio vs Meta Cloud API — log via `/flowflex:decision`; driver abstracts the choice**)
- Receive inbound WhatsApp messages into the shared inbox
- Send outbound messages (within 24h customer service window — driver enforces; outside window requires template)
- Template messages: pre-approved templates for messages outside the 24h window
- Template management: create, submit for approval, track approval status (provider sync)
- Template variables: `{{1}}`, `{{2}}` placeholder substitution
- Media messages: send/receive images, documents, location (via core.files)
- Phone numbers stored E.164 via `propaganistas/laravel-phone`
- Delivery + read receipts → `comms_messages.delivery_status`
- WhatsApp number registration and verification flow

---

## Data Model

### comms_whatsapp_config

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) unique | ulid | one config per company *(assumed: one number v1)* |
| channel_id | ulid FK comms_channels | |
| phone_number_e164 | string | |
| provider | string | 360dialog / twilio / meta |
| 🔐 api_key | text | encrypted |
| business_account_id | string | |
| 🔐 webhook_secret | text | encrypted cast — provider verify token |

### comms_whatsapp_templates

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | provider naming rules (lowercase_underscore) |
| category | string | marketing / utility / authentication |
| language | string | |
| body | text | with `{{n}}` placeholders |
| variables | jsonb | sample values |
| approval_status | string default `draft` | draft / pending / approved / rejected |
| external_template_id | string nullable | provider id |

Messages flow through `comms_messages` (channel_type = whatsapp).

---

## DTOs

### CreateTemplateData — name (regex provider rules), category (in set), language, body (placeholder syntax validated), variables[]
### SendTemplateData — conversation_id or phone_e164, template_id (approved), variable_values[] (count matches)

## Services & Actions

- `WhatsAppDriver implements ChannelDriverInterface` — `send()` checks 24h window (last inbound timestamp); outside → `TemplateRequiredException`; handles media
- `WhatsAppWebhookController` — signature/verify-token validated; normalises → `InboundMessageData` → inbox job; delivery/read receipts update message status
- `SubmitTemplateAction` / `SyncTemplateStatusJob` (provider polling)
- `ConnectWhatsAppAction::run(config): void` — verifies credentials with provider before save

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WhatsAppTemplateResource` | #1 CRUD resource | submit + approval status badge |
| `WhatsAppConfigPage` | #7 custom page (form) | connect number, credentials (write-only display) |

Sending happens through the Shared Inbox composer (template picker outside 24h window).


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('comms.whatsapp.view-any') && BillingService::hasModule('comms.whatsapp')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a throttle / rate limiter on the WhatsApp webhook route.
- **Upload contract** (medium): Specify MIME/extension whitelist, max size, and tenant-scoped storage path for WhatsApp media attachments.

---

## Permissions

`comms.whatsapp.manage-config` · `comms.whatsapp.manage-templates` (+ inbox permissions for messaging)

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `SyncTemplateStatusJob` | default | hourly | upsert by external_template_id |
| Webhook inbound processing | default | per webhook | external_id dedupe (inbox) |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] API key ciphertext; never re-displayed
- [ ] Webhook with bad verify token/signature → 403, nothing stored
- [ ] Inbound lands in inbox conversation (E.164 threading)
- [ ] Free-form send inside 24h window OK; outside → TemplateRequiredException
- [ ] Template send substitutes `{{n}}` values; unapproved template rejected
- [ ] Delivery/read receipts update message status
- [ ] Provider API mocked (`Http::fake`)

---

## Build Manifest

```
database/migrations/xxxx_create_comms_whatsapp_config_table.php
database/migrations/xxxx_create_comms_whatsapp_templates_table.php
app/Models/Comms/{WhatsAppConfig,WhatsAppTemplate}.php
app/Data/Comms/{CreateTemplateData,SendTemplateData}.php
app/Support/Comms/Drivers/WhatsAppDriver.php
app/Exceptions/Comms/TemplateRequiredException.php
app/Http/Controllers/Webhooks/WhatsAppWebhookController.php
app/Actions/Comms/{ConnectWhatsAppAction,SubmitTemplateAction}.php
app/Jobs/Comms/SyncTemplateStatusJob.php
app/Filament/Comms/Resources/WhatsAppTemplateResource.php
app/Filament/Comms/Pages/WhatsAppConfigPage.php
database/factories/Comms/WhatsAppTemplateFactory.php
tests/Feature/Comms/{WhatsAppDriverTest,WhatsAppWebhookTest,WhatsAppTemplateTest}.php
```

---

## Open Questions

- Provider choice (360dialog recommended in positioning) — **build-time ADR mandatory before this module starts**

---

## Related

- [[domains/communications/shared-inbox]]
- [[domains/communications/broadcast]]
- [[product/positioning]]
- [[architecture/patterns/encryption]]
