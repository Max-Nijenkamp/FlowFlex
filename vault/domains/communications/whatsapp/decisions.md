---
domain: communications
module: whatsapp
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# WhatsApp — Decisions

## ADR: Provider choice — BUILD-TIME ADR MANDATORY (source, open)

- **Context:** WhatsApp Business API access is via a BSP: 360dialog, Twilio, or Meta Cloud API direct.
- **Decision:** Deferred — a `/flowflex:decision` ADR is **required before this module is built**. 360dialog is recommended in [[../../../product/positioning]] (no per-message BSP markup, EU hosting). The `WhatsAppDriver` abstracts the choice so the rest of the module is provider-agnostic.
- **Consequences:** Config table carries `provider`; driver has provider-specific normalise/send. See [[unknowns]].

## ADR: 24h customer-service window enforced in the driver (source)

- **Decision:** `WhatsAppDriver::send` reads the last inbound timestamp. Inside 24h → free-form send allowed; outside → `TemplateRequiredException`, composer switches to the template picker.
- **Consequences:** Matches Meta's policy; keeps the window logic in one place.

## ADR: Templates mirror provider approval (source)

- **Decision:** Local `comms_whatsapp_templates` rows mirror provider approval status, synced hourly by `SyncTemplateStatusJob`, upserted by `external_template_id`. Only `approved` templates can be sent.

## ADR: Message rows owned by the inbox (source / data-ownership)

- **Decision:** WhatsApp never writes `comms_messages`. The driver normalises inbound to `InboundMessageData` and hands it to `InboxService`, which writes the row.
- **Consequences:** Preserves the bounded-context boundary ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[../../../product/positioning]]
