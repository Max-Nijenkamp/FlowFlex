---
domain: communications
module: internal-messaging
feature: channels-dms
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Channels & DMs

Public/private group channels and auto-created direct messages, with members-only visibility.

## Behaviour

- Public channel: anyone in the company can join (`JoinChannelAction`).
- Private channel: invite-only (`InviteToChannelAction`, member-gated).
- DM: auto-created per user pair, deduped by `dm_key`.
- Membership drives visibility (query + Reverb auth + search).

## UI

- **Kind**: custom-page (channel sidebar within the [[realtime-messaging|Internal Messaging]] page).
- **Layout**: left sidebar lists the user's channels + DMs, "+" to create/join; unread badges.
- **Key interactions**: create channel (name, type, members) · join public · invite to private · click user → open DM.
- **States**: empty (no channels → "start a channel / DM" CTA) · loading (sidebar skeleton) · error (not a member → 403) · selected (active channel highlighted).
- **Gating**: `comms.internal.use`; `comms.internal.manage-channels` to administer.

## Data

- Owns / writes: `comms_channels_internal`, `comms_channel_members` (own module).
- Reads: company users (RBAC) for member pickers — read-only.
- Cross-domain writes: none.

## Relations

- Consumes: company user directory (RBAC).
- Feeds: membership gates realtime + search.
- Shared entity: `users` (RBAC, read-only).

## Related

- [[../_module|Internal Messaging]] · [[realtime-messaging]] · [[threads-reactions]]
