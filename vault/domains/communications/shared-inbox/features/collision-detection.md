---
domain: communications
module: shared-inbox
feature: collision-detection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Collision Detection

Warns an agent when another teammate is already replying to the same conversation — prevents the classic double / contradictory reply.

## Behaviour

- When an agent opens a conversation and starts composing, a client-to-client Reverb **whisper** on `company.{id}.comms` announces presence on that conversation.
- Other viewers of the same conversation see a "‹name› is replying…" banner and a soft warning on the composer.
- Whisper is ephemeral (no DB row) *(assumed)*; it clears when the agent leaves or sends.

## UI

- **Kind**: widget (in-page banner on the inbox thread pane; part of the [[unified-conversation-view]] custom page).
- **Layout**: inline banner above the composer.
- **Key interactions**: focus composer → broadcast whisper; receive whisper → show banner; send/leave → clear.
- **States**: idle (no banner) · colliding (banner + composer warning) · error (whisper channel down → degrade silently, no banner).
- **Gating**: `comms.inbox.reply` (only repliers whisper/see).

## Data

- Owns / writes: nothing persisted — whisper only *(assumed; see [[../unknowns]])*.
- Reads: nothing cross-domain.
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: Reverb whisper on `company.{id}.comms` (UI only, not a bus event).
- Shared entity: none.

## Related

- [[../_module|Shared Inbox]] · [[unified-conversation-view]] · [[../../../architecture/websockets]]
