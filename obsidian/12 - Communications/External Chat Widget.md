---
tags: [flowflex, domain/communications, chat-widget, live-chat, customer, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-08
---

# External Chat Widget

Customer-facing live chat for your website. When a visitor starts a chat, it appears in FlowFlex's shared inbox. Agents respond from the same platform they use for everything else. No Intercom subscription required.

**Who uses it:** Customer support teams, sales teams (for website visitors)
**Filament Panel:** `communications` (agent side); JavaScript widget (customer side)
**Depends on:** Core, [[Shared Inbox & Email]] (CRM), [[Internal Messaging & Chat]] (routing), [[Customer Support & Helpdesk]]
**Phase:** 5
**Build complexity:** Very High — public JS widget, real-time, anonymous visitors

---

## Features

### Website Widget (Customer Side)

- Embed script: one `<script>` tag in any website
- Customisable: brand colour, logo, greeting message, agent avatar
- Launcher button: floating bottom-right (position configurable)
- Pre-chat form: optional (collect name, email, topic before starting)
- Chat window: message input, file attachment, emoji
- Offline message: when no agents available, shows contact form
- Mobile responsive: full-screen modal on mobile
- Language detection: auto-sets language from browser locale (if translations provided)

### Agent Side (FlowFlex)

- Conversations appear in dedicated **Inbox: Live Chat** section
- Unread badge count on inbox
- Shows: visitor name/email (if provided or identified), page they're on, time on site, previous conversations
- Typing indicator (agent sees visitor typing, visitor sees agent typing)
- Rich text reply: bold, links, inline images, canned responses
- File attachment: send images, documents, files from S3 library
- Transfer: hand conversation to another agent or team
- Note: leave internal note (not visible to customer)
- Resolve: marks conversation closed → moves to resolved inbox

### Canned Responses

- Library of saved replies for common questions
- Search by keyword while typing
- Insert with one click (replaces typing the same answer 20× a day)
- Personal canned responses (agent's own) + team shared responses

### Visitor Identification

- Anonymous by default (no login required)
- If visitor is a FlowFlex CRM contact (matched by email from pre-chat form):
  - Shows CRM contact card in agent sidebar
  - Chat transcript auto-appended to CRM contact timeline
  - Agent can see: past tickets, deals, purchase history
- Identity API: websites can pass identity data programmatically

### Routing Rules

- Round-robin to all online agents
- Or: route by skill/team tag
- Or: specific page rule (e.g. `/pricing` → route to Sales team)
- Overflow: if no agents online → show contact form → creates support ticket

### AI Features

- Draft reply suggestions (AI reads conversation context, suggests reply)
- Sentiment detection: flag distressed or angry customers
- Auto-classify: tag conversation with topic on first message
- Auto-resolution: if AI is confident it can answer, offer AI-only response first

### Analytics

- Volume by hour/day/week
- First response time (FRT) median
- Resolution time median
- Customer satisfaction (CSAT): prompt after resolution ("Rate this conversation")
- Busiest hours heatmap
- Agent-level stats: conversations handled, FRT, CSAT score

---

## Database Tables (4)

### `chat_widget_configs`
| Column | Type | Notes |
|---|---|---|
| `embed_key` | string unique | public key for script tag |
| `name` | string | widget name (for multi-widget setups) |
| `brand_color` | string | hex |
| `logo_file_id` | ulid FK nullable | |
| `greeting_message` | text | |
| `offline_message` | text | |
| `pre_chat_form_enabled` | boolean | |
| `pre_chat_fields` | json | name, email, topic etc. |
| `routing_team_id` | ulid FK nullable | |
| `ai_auto_reply_enabled` | boolean | |
| `csat_enabled` | boolean | |

### `chat_widget_conversations`
| Column | Type | Notes |
|---|---|---|
| `config_id` | ulid FK | → chat_widget_configs |
| `visitor_id` | string | anonymous session ID |
| `visitor_name` | string nullable | |
| `visitor_email` | string nullable | |
| `crm_contact_id` | ulid FK nullable | if identified |
| `assigned_agent_id` | ulid FK nullable | |
| `status` | enum | `open`, `pending`, `resolved` |
| `page_url` | string nullable | page visitor was on |
| `started_at` | timestamp | |
| `resolved_at` | timestamp nullable | |
| `csat_score` | integer nullable | 1-5 |

### `chat_widget_messages`
| Column | Type | Notes |
|---|---|---|
| `conversation_id` | ulid FK | |
| `author_type` | enum | `visitor`, `agent`, `system`, `ai` |
| `author_id` | ulid FK nullable | agent id if agent |
| `body` | text | |
| `is_note` | boolean | internal note |
| `attachments` | json nullable | |
| `sent_at` | timestamp | |

### `chat_canned_responses`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `content` | text | |
| `shortcut` | string nullable | keyboard shortcut key |
| `is_shared` | boolean | team vs personal |
| `owner_id` | ulid FK nullable | if personal |
| `team_id` | ulid FK nullable | if team-scoped |

---

## Permissions

```
communications.chat-widget.configure
communications.chat-widget.respond
communications.chat-widget.view-conversations
communications.chat-widget.view-analytics
communications.chat-widget.manage-canned-responses
```

---

## Competitor Comparison

| Feature | FlowFlex | Intercom | Drift | Tidio |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€74+/mo) | ❌ (€400+/mo) | ❌ (€29+/mo) |
| CRM contact auto-linking | ✅ | ✅ | ✅ | partial |
| AI draft replies | ✅ | ✅ (Fin AI €€) | ✅ | ✅ |
| CSAT collection | ✅ | ✅ | ✅ | ✅ |
| Routing rules | ✅ | ✅ | ✅ | ✅ |
| Integrated with support tickets | ✅ | ✅ | partial | partial |

---

## Related

- [[Communications Overview]]
- [[Internal Messaging & Chat]]
- [[Customer Support & Helpdesk]]
- [[Shared Inbox & Email]]
- [[Contact & Company Management]]
