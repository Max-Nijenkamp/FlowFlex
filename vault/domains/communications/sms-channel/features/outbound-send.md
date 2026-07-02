---
domain: communications
module: sms-channel
feature: outbound-send
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Outbound Send

Send SMS from the company virtual number, with segment estimation and opt-out enforcement.

## Behaviour

- `SmsDriver::send` checks `OptOutService::isOptedOut` first → `RecipientOptedOutException` if opted out.
- Estimates segments (160 GSM / 70 unicode *(assumed)*) so the composer shows a counter.
- Sends via provider from `virtual_number_e164`; records cost from the callback.

## UI

- **Kind**: widget (composer behaviour inside the [[../../shared-inbox/features/unified-conversation-view|Shared Inbox]]; segment counter shown). Connection setup is `SmsChannelResource` (#1 resource).
- **Layout**: reply composer with a live segment/char counter.
- **Key interactions**: type → counter updates → send; opted-out recipient → blocked with a message.
- **States**: default · sending · error (opted-out / provider failure).
- **Gating**: `comms.inbox.reply`.

## Data

- Owns / writes: nothing directly (uses `comms_sms_config` read).
- Reads: `comms_sms_optouts` via `OptOutService`; `comms_sms_config` (own module).
- Cross-domain writes: none — the outbound message row is written by the inbox via `InboxService` ([[../../../security/data-ownership]]).

## Relations

- Consumes: opt-out state (own service).
- Feeds: `InboxService::send` → driver (inbox owns the row).
- Shared entity: `comms_messages` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Related

- [[../_module|SMS Channel]] · [[inbound-optout]] · [[cost-tracking]]
