---
domain: communications
module: internal-messaging
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Internal Messaging — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `InternalChatService::dmWith` | `dmWith(userId): ChannelData` | Find-or-create a DM channel by `dm_key` (sorted user-id pair hash). |
| `InternalChatService::post` | `post(PostMessageData): MessageData` | Persists a message, broadcasts `InternalMessagePosted`, fires @mention notifications. |
| `JoinChannelAction` | public channels only | Add the current user as a member. |
| `InviteToChannelAction` | private channels, member-only | Add a member by invite. |
| `MarkReadAction` | | Update `last_read_at` for the user's membership. |
| `ToggleReactionAction` | | Toggle an emoji reaction in `reactions` jsonb. |

Channel authorisation lives in `routes/channels.php`: a membership check per internal channel gates the Reverb private/presence channel.

## Events

| Event | Kind | Notes |
|---|---|---|
| `InternalMessagePosted` | Internal `ShouldBroadcast` (Reverb) | Broadcast on the channel's presence channel. Not a cross-domain bus event. |

No event-bus domain-events. @mention → a `core.notifications` notification (that module writes its own rows). See [[../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Messaging

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `InternalMessagingPage` | #8 chat custom page | [[../../../architecture/patterns/page-blueprints#Inbox / Chat / Conversation]] | channel sidebar + thread pane; Reverb presence + typing whispers; cursor-paginated history |

**Access contract (mandatory):** `InternalMessagingPage` gates on
`canAccess() = Auth::user()->can('comms.internal.use') && BillingService::hasModule('comms.internal')`
per [[../../../architecture/filament-patterns]] #1 — as a custom page it states this explicitly. All users hold
`comms.internal.use` by default; `comms.internal.manage-channels` gates channel admin ([[./security]]). Private-channel /
DM visibility is a second, membership scope enforced in query + Reverb channel-auth + search (see [[./security]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Channel CRUD (create / admin) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Message post + membership join/invite | n/a | Append-only inserts (`comms_internal_messages`, `comms_channel_members`); DM dedupe via `dm_key`, membership unique per `(channel, user)` |
| Reaction toggle (`reactions` jsonb) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the message row — jsonb read-modify-write, prevents lost toggles from concurrent reactors |
| Mark-read (`last_read_at`) | n/a | Each user writes only their own membership row — no cross-writer contention |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

- **Meilisearch:** message bodies — results **post-filtered** to channels the user is a member of *(assumed)*. See [[../../../architecture/search]].
- **Reverb:** presence channel per internal channel — new messages + typing whispers (ui-strategy row #8). See [[../../../architecture/websockets]].

## Visibility (second scope)

Beyond `CompanyScope`, private-channel and DM content is readable by **members only** — enforced in the query, the Reverb channel auth, and the search post-filter (three layers).

## Implementation Notes (tense-softened)

- DMs are designed to **dedupe** on a sorted user-id pair hash (`dm_key`), so `dmWith` is idempotent.
- The message feed is designed to be **cursor-paginated** on `(channel_id, created_at)`.
- Membership is designed to be the single visibility gate, checked in query + channel-auth + search.

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/websockets]]
