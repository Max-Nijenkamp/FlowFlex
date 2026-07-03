---
domain: communications
module: whatsapp
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# WhatsApp

Planned WhatsApp Business API channel — send and receive WhatsApp messages through the shared inbox, and manage pre-approved message templates. **The key EU SMB differentiator** (see [[../../../product/positioning]]).

> This module is planned for build. It plugs into the shared inbox as a `ChannelDriver`; it owns only its config + templates tables and never writes message rows directly.

## Module-key

`comms.whatsapp`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.whatsapp`  
**Tables:** `comms_whatsapp_config`, `comms_whatsapp_templates`  
**Encrypted fields:** `comms_whatsapp_config.api_key`, `comms_whatsapp_config.webhook_secret`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../shared-inbox/_module\|comms.inbox]] | registers the `whatsapp` `ChannelDriver`; message rows flow through `comms_messages` (owned by the inbox) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | webhook processing, template-status sync jobs |
| Soft | [[../broadcast/_module\|comms.broadcast]] | template broadcasts |

## Core Features

- **WhatsApp Business API integration** via a provider (**build-time ADR required: 360dialog vs Twilio vs Meta Cloud API — the `WhatsAppDriver` abstracts the choice**).
- **Inbound receive** — WhatsApp messages land in the shared inbox, threaded by E.164 phone number.
- **Outbound send** — free-form messages inside the 24h customer-service window; outside the window a pre-approved template is required (`TemplateRequiredException`).
- **Template management** — create, submit for provider approval, and track approval status (`draft / pending / approved / rejected`), synced from the provider.
- **Template variables** — `{{1}}`, `{{2}}` placeholder substitution.
- **Media messages** — send/receive images, documents, location (via core.files).
- **Delivery + read receipts** — update `comms_messages.delivery_status`.
- **Number registration + verification** — connect + verify credentials before save.

## See features/

- [[features/template-management|Template Management]] — create/submit templates and track provider approval.
- [[features/window-sending|Window Sending]] — 24h-window rule; free-form inside, template required outside.
- [[features/inbound-webhook|Inbound Webhook]] — provider webhook → normalise → inbox; delivery/read receipts.

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

## Test Checklist

- [ ] Tenant isolation: webhook resolves company from `webhook_secret` / number; templates + config never cross companies.
- [ ] Module gating: template resource + config page hidden when `comms.whatsapp` inactive.
- [ ] API key stored as ciphertext; never re-displayed.
- [ ] Webhook with bad verify token/signature → 403, nothing stored.
- [ ] Inbound lands in inbox conversation (E.164 threading).
- [ ] Free-form send inside 24h window OK; outside → `TemplateRequiredException`.
- [ ] Template send substitutes `{{n}}` values; unapproved template rejected.
- [ ] Delivery/read receipts update message `delivery_status`.
- [ ] Provider API mocked (`Http::fake`) — no real calls in tests.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Registers | `ChannelDriver` (`whatsapp`) | [[../shared-inbox/_module\|comms.inbox]] | driver contract; inbox owns `comms_messages` |
| Writes (via inbox) | `InboundMessageData` → `InboxService` | [[../shared-inbox/_module\|comms.inbox]] | inbox writes the message row — whatsapp does not |
| Reads | provider WhatsApp Business API | provider (360dialog / Twilio / Meta) | send, template submit/status, media |
| Soft | template broadcast | [[../broadcast/_module\|comms.broadcast]] | approved templates used for broadcasts |

No cross-domain **domain events** are fired or consumed (see [[../../../architecture/event-bus]] for the platform contract).

**Data ownership:** `comms.whatsapp` writes **only** `comms_whatsapp_config` and `comms_whatsapp_templates`. Outbound + inbound **message rows** live in `comms_messages`, owned by [[../shared-inbox/_module\|comms.inbox]]: the `WhatsAppDriver` hands normalised `InboundMessageData` to `InboxService`, which writes the row — whatsapp never writes `comms_messages` directly ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] · [[../broadcast/_module|Broadcast]] · [[../../../product/positioning|Positioning]]
- [[../../../architecture/patterns/encryption]] · [[../../../glossary]]
