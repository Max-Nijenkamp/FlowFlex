---
domain: communications
module: email-channel
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Channel — Decisions

## ADR: v1 = forwarding, OAuth deferred (source, assumed)

- **Context:** Connecting a shared team inbox (support@, info@).
- **Decision:** v1 uses **email forwarding** to a unique FlowFlex inbound address (`{token}@inbound.flowflex.io`) *(assumed)*. OAuth (Gmail/Outlook) is deferred to v1.x (`oauth_token` column reserved, encrypted).
- **Consequences:** No OAuth complexity at launch; the user sets up a forward rule. See [[unknowns]].

## ADR: Distinct from `crm.email` (source)

- **Decision:** This module = **shared team addresses**; `crm.email` = per-rep **personal mailbox sync**. Separate modules, separate tables.
- **Consequences:** No overlap; a company can run both. See [[../../crm/email-integration/_module]].

## ADR: Threading via headers with subject fallback (source)

- **Decision:** Match replies via `References`/`In-Reply-To` message-id headers; fall back to `(channel, from-address)` open conversation, then subject.

## ADR: Outbound via Resend (source, assumed)

- **Decision:** Send from the connected address via Resend with custom `from` + reply-to threading headers + injected signature *(assumed provider)*.
- **Consequences:** Depends on Resend inbound-relay for the webhook path. See [[unknowns]].

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/email]]
