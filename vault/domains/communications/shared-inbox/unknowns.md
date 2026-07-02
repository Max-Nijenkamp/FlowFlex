---
domain: communications
module: shared-inbox
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shared Inbox — Unknowns

## Assumed Items

- Instagram / Facebook channels are post-P2 *(assumed)*.
- Meilisearch indexes a rolling window of message bodies (latest ~1k msgs per conversation aggregate doc) *(assumed)*.
- `comms_messages.meta` jsonb column carries provider metadata incl. `cost_cents` (used by SMS) *(assumed — not in the original column list)*.
- GDPR: erased-contact conversations are unlinked, bodies retained as company records *(assumed)*.
- Automations subscribe to inbound via their own soft integration (inbox fires no event) *(assumed)*.

## Open Questions

- Should inbound/outbound emit a `ConversationMessageLogged` bus event so `crm.activities` can log a touch on the contact timeline? Currently modelled as read-only + no event ([[decisions]]).
- Collision detection is a client-to-client Reverb whisper — does it need a server-side "who's viewing" presence record for reliability?
- Snooze auto-reopen cadence (15 min command) vs. exact-time scheduling — acceptable latency?
- Attachment retention / virus scanning policy beyond the MIME whitelist.

> [!warning] UNVERIFIED
> Cross-domain event contract is undecided. The vault currently states no domain-events; a CRM timeline integration would likely need one.

## Related

- [[_module]] · [[decisions]] · [[../../../security/data-ownership]]
