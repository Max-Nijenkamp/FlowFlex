---
domain: communications
module: internal-messaging
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Internal Messaging — Decisions

## ADR: Standalone model, not the shared inbox (source)

- **Context:** Internal team chat vs. customer conversations.
- **Decision:** Internal messaging uses its **own** tables + model, not the `comms_conversations` shared-inbox model. Internal users only.
- **Consequences:** No channel-driver contract; no external parties; simpler visibility model.

## ADR: DM dedupe via `dm_key` (source)

- **Decision:** A DM channel is keyed by a sorted user-id pair hash (`dm_key`, unique). `dmWith` is find-or-create.
- **Consequences:** Exactly one DM channel per user pair.

## ADR: Three-layer visibility for private/DM (source)

- **Decision:** Membership is enforced in the query, the Reverb channel auth, and the search post-filter.
- **Consequences:** No single-point leak; all three must be tested ([[security]]).

## ADR: @mention delivered by core.notifications (data-ownership)

- **Decision:** An @mention fires a notification handled by `core.notifications`, which writes its own rows. Internal-messaging writes only its own tables.
- **Consequences:** Bounded-context boundary preserved ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/websockets]]
