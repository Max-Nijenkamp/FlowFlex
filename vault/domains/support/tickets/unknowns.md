---
domain: support
module: tickets
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Tickets — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Inbound email provider** — parse webhook assumed Resend/Postmark inbound *(assumed)*. Confirm the provider + payload shape before building `handleInboundEmail`.
- **`waiting_on_customer` toggle** — assumed an explicit agent toggle sets this state on a public reply *(assumed)*; could instead be automatic.
- **Reopen window** — default 14 days *(assumed)*; should be a company setting.
- **Auto-close delay** — `resolved → closed` after 3 days *(assumed)*; should be a company setting.

## Open Questions

- Should ticket numbers be strictly gapless sequential (locking cost) or best-effort per company? Assumed a per-company sequence with unique constraint.
- Is the public web form embed in v1 scope or deferred? Marked *(assumed: optional embed)*.

## Related

- [[./decisions]]
- [[../_index|Support MOC]]
