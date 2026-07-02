---
domain: crm
module: email-integration
type: feature
feature: oauth-connection
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: OAuth Connection

Planned per-user mailbox connection to Gmail or Outlook via Google/Microsoft OAuth apps, with encrypted token storage.

## Flow

1. User initiates connect from `EmailConnectionResource` → redirect to provider consent.
2. Provider redirects back to `EmailOAuthController`; the callback **verifies `state` + PKCE** before proceeding (see [[../../../../security/webhooks-signing]]).
3. Access + refresh tokens are stored in the encrypted `oauth_token` blob on a `crm_email_connections` row, unique per `(user_id, provider)`.
4. Connection defaults: `sync_enabled = true`, `default_visibility = shared`.

## Disconnect

- `DisconnectMailboxAction::run(connectionId)` revokes the provider token, stops sync, and **keeps** already-synced mail.

## Security

- OAuth token encrypted at rest — see [[../../../../security/encryption]].
- Callback isolated with state + PKCE verification — see [[../../../../security/webhooks-signing]].

## Test Checklist

- [ ] OAuth tokens stored as ciphertext in DB; disconnect revokes.

## UI
- **Kind**: custom-page — a "Connect your inbox" settings page with the OAuth button + connection status. (Chosen over simple-resource because the OAuth handshake/redirect and live connection state don't fit a CRUD table; the connected-accounts list can still be a simple-resource, `EmailConnectionResource`.)
- **Page**: Connect Inbox settings page → `/crm/settings/email` *(assumed route)*; provider callback handled by `EmailOAuthController`.
- **Layout**: provider cards (Gmail / Outlook) with Connect buttons, current connection status, default-visibility toggle, and a Disconnect action.
- **Key interactions**: click Connect → provider consent redirect → callback verifies `state` + PKCE → connection row created; toggle `sync_enabled` / `default_visibility`; Disconnect revokes token.
- **States**: empty (no mailbox connected → connect CTA) · loading (redirecting/awaiting callback) · error (consent denied / token exchange failure) · selected (connected mailbox shows status + disconnect)
- **Gating**: `crm.email.connect` *(assumed)* to connect/disconnect own mailbox

## Data
- Owns / writes: `crm_email_connections` (encrypted `oauth_token` blob, `sync_enabled`, `default_visibility`, unique `(user_id, provider)`)
- Reads: authenticated user identity; provider OAuth endpoints
- Cross-domain writes: via events only ([[../../../../security/data-ownership]])

## Relations
- Consumes: OAuth provider handshake from a core/integrations provider *(assumed)*
- Feeds: `MailboxConnected` *(assumed)* → enables [[inbound-sync|inbound-sync]] scheduling
- Shared entity: user account (owned by core.rbac)

## Related

- [[../architecture]] · [[../security]] · [[../api]]
