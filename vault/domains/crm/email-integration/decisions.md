---
domain: crm
module: email-integration
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Integration — Decisions

## ADR: Shared vs private visibility model

- **Context:** Synced and sent emails may be personal correspondence; some should be visible to the whole CRM team, some only to the mailbox owner.
- **Decision:** Each connection carries a `default_visibility` (`shared` / `private`); each email may override it via its own `visibility`. Private emails are readable only by the connection owner — enforced by a query scope that excludes even `view-any` holders.
- **Consequences:** Timeline/thread queries must always apply the visibility scope; `view-shared` is the general grant, private mail is owner-only.

## ADR: Scheduled pull first, provider webhooks later

- **Context:** Inbound sync can be driven by a scheduled pull or by provider push webhooks.
- **Decision:** v1 uses a **scheduled incremental pull** (`SyncMailboxesCommand`, every 10 min, per-connection cursor + `message_id` dedupe). Provider webhooks are deferred to v1.x *(assumed)*.
- **Consequences:** Simpler v1 with a small latency; dedupe on `message_id` keeps the eventual webhook path idempotent. Revisit when webhooks land.

## ADR: Encrypt OAuth tokens at rest

- **Context:** Access + refresh tokens grant mailbox access.
- **Decision:** Store them in a single encrypted `oauth_token` blob (`encrypted` cast, `text` column). See [[../../../security/encryption]].
- **Consequences:** Tokens never appear in plaintext in the DB; disconnect must also revoke provider-side.

## ADR: Purify email bodies before storage

- **Context:** Inbound HTML can carry XSS.
- **Decision:** Purify `body` (htmlpurifier) before persisting.
- **Consequences:** Stored bodies are safe to render in threads; tested with an XSS fixture.
