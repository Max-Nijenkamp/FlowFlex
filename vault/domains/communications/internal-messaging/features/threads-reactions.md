---
domain: communications
module: internal-messaging
feature: threads-reactions
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Threads, Reactions & Mentions

Threaded replies, emoji reactions, @mentions with notifications, and membership-scoped search.

## Behaviour

- Reply in-thread: `parent_message_id` links a reply to its parent.
- Reactions: `ToggleReactionAction` toggles `{emoji: [user_ids]}` in the message `reactions` jsonb.
- @mention: parsing the body → fires a `core.notifications` notification to the mentioned user.
- Search: Meilisearch over message bodies, post-filtered to the user's member channels *(assumed)*.

## UI

- **Kind**: widget (thread pane + reaction bar + search box within the [[realtime-messaging|Internal Messaging]] custom page).
- **Layout**: message row with reaction chips + "reply in thread"; thread opens a side/nested pane; search box in the header.
- **Key interactions**: hover message → react/reply; @type → member autocomplete; search → jump to result (member channels only).
- **States**: default · reacting (chip toggles) · threaded (nested view) · search-empty (no results in your channels).
- **Gating**: `comms.internal.use`; search + threads respect membership.

## Data

- Owns / writes: `comms_internal_messages` (thread rows, `reactions`) — own module.
- Reads: company user directory for @mention autocomplete (RBAC).
- Cross-domain writes: none — @mention notifications written by `core.notifications` ([[../../../security/data-ownership]]).

## Relations

- Consumes: user directory (RBAC).
- Feeds: @mention → `core.notifications`.
- Shared entity: `users` (RBAC, read-only).

## Test Checklist

### Unit
- [ ] `ToggleReactionAction` toggles `{emoji: [user_ids]}` in the `reactions` jsonb (add then remove)
- [ ] @mention parsing extracts mentioned user ids from the body

### Feature (Pest)
- [ ] Concurrent reactions from two users on one message both persist (row lock — no lost jsonb update)
- [ ] @mention fires a `core.notifications` notification (that module writes its own rows)
- [ ] Search is post-filtered to the user's member channels — a non-member never sees a hit

### Livewire
- [ ] Reaction chip toggles; reply opens the thread pane; @autocomplete lists company users only

## Related

- [[../_module|Internal Messaging]] · [[realtime-messaging]] · [[../../core/notifications/_module|Notifications]]
