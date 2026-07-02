---
domain: support
module: live-chat
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Live Chat — Security

## Permissions

| Permission | Description |
|---|---|
| `support.chat.respond` | Respond to chats from the queue |
| `support.chat.view-transcripts` | View archived transcripts |
| `support.chat.manage-widget` | Configure the widget key/settings |

Seeded in `PermissionSeeder`.

## Access Contract (panel)

```php
canAccess() = Auth::user()->can('support.chat.view-any')
           && BillingService::hasModule('support.chat')
```

Per [[../../../architecture/filament-patterns]] #1 — `ChatQueuePage` states this explicitly.

## Public Widget Guard (HIGH — per [[build/security-audit-2026-06-11]])

- Widget HTTP endpoints run under an **explicit scoped guard** (Sanctum stateless / dedicated widget guard) limited to `widget-key + per-chat token` scope — **not** the panel session guard.
- Reverb channel auth: a visitor's signed token authorises **only** its own `chat.{chat_id}` presence/private channel; it can never subscribe to a company-wide channel or another chat (cross-chat auth test required).
- Invalid widget keys rejected; all widget endpoints rate-limited.

## Content Safety & Privacy

- Message bodies purified, max 4000 chars.
- IP-geo deferred *(assumed: privacy + effort)*; only page URL + user-agent stored.

## Tenant Isolation

All tables carry `company_id` (global `CompanyScope`) — [[../../../architecture/multi-tenancy]].

## Encrypted Fields

None planned for v1.
