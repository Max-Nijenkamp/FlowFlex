---
domain: support
module: live-chat
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Live Chat — Security

## Permissions

| Permission | Description |
|---|---|
| `support.chat.view-any` | Access the chat queue / panel (gate cited by `canAccess()`) |
| `support.chat.respond` | Claim + respond to chats; set availability; convert a chat to a ticket |
| `support.chat.view-transcripts` | View archived transcripts |
| `support.chat.manage-widget` | Configure the widget key/settings |

Seeded in `PermissionSeeder`. `ConvertChatToTicketAction` and `SetAvailabilityAction` are gated by `support.chat.respond`; the created ticket is additionally gated inside `TicketService`.

## Access Contract (panel)

```php
canAccess() = Auth::user()->can('support.chat.view-any')
           && BillingService::hasModule('support.chat')
```

Per [[../../../architecture/filament-patterns]] #1 — `ChatQueuePage` states this explicitly.

## Public Widget Guard (HIGH — per [[_archive/build-history/security-audit-2026-06-11]])

- Widget HTTP endpoints run under an **explicit scoped guard** (Sanctum stateless / dedicated widget guard) limited to `widget-key + per-chat token` scope — **not** the panel session guard.
- Reverb channel auth: a visitor's signed token authorises **only** its own `chat.{chat_id}` presence/private channel; it can never subscribe to a company-wide channel or another chat (cross-chat auth test required).
- Invalid widget keys rejected; all widget endpoints run under a named `chat-widget` limiter *(assumed)* keyed by widget-key + visitor token ([[../../../architecture/security]] rate-limit registry) — public unauthenticated endpoints require a cited limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Content Safety & Privacy

- Message bodies purified, max 4000 chars.
- IP-geo deferred *(assumed: privacy + effort)*; only page URL + user-agent stored.

## Tenant Isolation

All tables carry `company_id` (global `CompanyScope`) — [[../../../architecture/multi-tenancy]].

## Encrypted Fields

None planned for v1.
