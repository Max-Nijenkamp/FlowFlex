---
domain: support
module: live-chat
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Live Chat — DTOs & API

## DTOs

### StartChatData (public widget input)

| Field | Type | Validation |
|---|---|---|
| widget_key | string | valid company widget key |
| visitor_name | ?string | nullable |
| visitor_email | ?string | nullable, email |
| message | string | required, max:4000 |
| page_url | string | |

### ChatMessageData (input)

`chat_id`, `body` (required, max:4000). Sender resolved from auth context (agent session) or per-chat visitor token.

### ChatData (output)

`id`, `status`, `agent_name`, `visitor_name`, `messages[]`, `ticket_id`, `started_at`.

---

## Public / Portal Endpoints (widget)

Scoped widget guard (widget-key + per-chat signed token, rate-limited):

| Route | Purpose |
|---|---|
| `POST /chat/start` | `ChatService::start` — validates widget key |
| `POST /chat/{chat}/message` | `ChatService::sendMessage` — per-chat token scope |
| `GET /chat/widget.js` | `ChatWidgetController` — built embed asset |

Reverb channel auth (`routes/channels.php`): visitor token authorises **only** its own `chat.{chat_id}` — never a company-wide channel. See [[./security]].
