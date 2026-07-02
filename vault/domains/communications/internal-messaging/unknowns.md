---
domain: communications
module: internal-messaging
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Internal Messaging — Unknowns

## Assumed Items

- Search results are post-filtered to member channels *(assumed)* — vs. filtering at the Meilisearch query level.
- All users get `comms.internal.use` by default *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> Whether Meilisearch membership filtering is a **post-filter** (risk: leaking result counts / snippets) or an index-level filter (safer) is undecided. For a data-leak-sensitive feature this should be index-level with per-channel filter attributes.

- Message edit/delete + retention policy (GDPR on internal chat).
- Presence/typing at scale — Reverb connection budget per company.
- Guest/cross-company channels (probably out of scope) — confirm.
- Notification preferences (mute channel, DND) — owned here or by `core.notifications`?

## Related

- [[_module]] · [[decisions]] · [[security]]
