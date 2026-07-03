---
domain: communications
module: whatsapp
feature: window-sending
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Window Sending (24h rule)

Enforces WhatsApp's customer-service window: free-form messages inside 24h of the last inbound; a pre-approved template outside it.

## Behaviour

- `WhatsAppDriver::send` reads the last inbound message timestamp for the conversation.
- Inside 24h → free-form send allowed.
- Outside 24h → raises `TemplateRequiredException`; the inbox composer switches to the template picker (approved templates only), substitutes `{{n}}` values.
- Media send supported inside the window.

## UI

- **Kind**: widget (composer behaviour inside the [[../../shared-inbox/features/unified-conversation-view|Shared Inbox]] custom page — no standalone page).
- **Layout**: composer shows free-text inside window; outside window it swaps to an approved-template picker + variable fields.
- **Key interactions**: type + send inside window; outside → pick template → fill variables → send.
- **States**: in-window (free text) · out-of-window (template-only) · error (unapproved template / variable count mismatch → validation error).
- **Gating**: `comms.inbox.reply`.

## Data

- Owns / writes: nothing directly — send goes through `InboxService`, which writes `comms_messages` (inbox-owned).
- Reads: `comms_whatsapp_templates` (own module) for the picker.
- Cross-domain writes: none — the message row is written by the inbox, not this module ([[../../../security/data-ownership]]).

## Relations

- Consumes: last-inbound timestamp from the conversation (inbox).
- Feeds: outbound message via `InboxService::send` (inbox owns the row).
- Shared entity: `comms_messages` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Test Checklist

### Unit
- [ ] Window check: `now - lastInboundAt <= 24h` allows free-form; beyond it requires a template
- [ ] `{{n}}` substitution: variable count mismatch is rejected

### Feature (Pest)
- [ ] Free-form send inside the 24h window succeeds via `WhatsAppDriver::send`
- [ ] Send outside the window raises `TemplateRequiredException`; an unapproved template is rejected

### Livewire
- [ ] Composer swaps to the approved-template picker outside the window; send denied without `comms.inbox.reply`

## Related

- [[../_module|WhatsApp]] · [[template-management]] · [[../../shared-inbox/_module|Shared Inbox]]
