---
type: module
domain: Communications
panel: comms
module-key: comms.live-chat
status: planned
color: "#4ADE80"
---

# Live Chat Widget

> An embeddable chat widget for the company's own website — customisable, AI-first, real-time, and fully integrated with the Omnichannel Inbox — without Intercom or Crisp.

**Panel:** `/comms`
**Module key:** `comms.live-chat`

## What It Does

Live Chat Widget gives every FlowFlex company an embeddable chat widget they can add to their website with a single script tag. Visitors can chat in real time with support or sales agents, the AI handles first response by searching the knowledge base before routing to a human, and offline mode automatically creates a support ticket when no agent is available. All conversations flow into the Omnichannel Inbox — agents manage live chat alongside email, WhatsApp, and other channels from one queue. Visitor identity is captured (anonymous UUID or JWT-identified for logged-in users) and linked to CRM contacts automatically.

## Features

### Core
- Widget script: a single `<script>` snippet generated in settings and pasted into the company's website `<head>` — initialises the chat widget with the company's configuration
- Widget customisation: configure widget colors (primary colour, background, text), position (bottom-right or bottom-left), launcher icon (default chat bubble or custom SVG), greeting message (shown in the widget before the visitor types), and agent avatar (team photo or custom image)
- Visitor identification: anonymous visitors get a UUID stored in `localStorage` — if the company's website uses FlowFlex authentication, a JWT can be passed to the widget via a JavaScript method (`FlowFlexWidget.identify({name, email, ...})`) to identify the visitor as a known contact
- Real-time messaging: messages are delivered via Reverb WebSocket on the `presence-chat-widget.{company_id}` channel — both visitor and agent sides are connected; typing indicators and read receipts are included
- Agent assignment: new chat sessions are routed to available agents based on the Omnichannel Inbox routing rules (round-robin or manual assignment)
- Offline mode: when no agent is available (outside hours or all busy), the widget displays an offline message and a contact form — submissions create a `support_tickets` entry (Support domain) with source `live_chat`

### Advanced
- AI first response: before routing to a human agent, the AI searches the knowledge base (`kb_articles`, Support domain) for relevant answers to the visitor's first message and sends a suggested response — the human agent sees the AI suggestion and can send, edit, or dismiss it; the AI response appears immediately (<2s) so the visitor gets a fast first response even if all agents are busy
- Visitor context panel: agents see a sidebar on each chat showing the visitor's browsing history on the current session (pages viewed, time on site), CRM contact record (if identified), and any open support tickets — all in the agent interface within the Omnichannel Inbox
- Conversation history: returning visitors (identified by UUID or JWT) see their previous chat history when they open the widget again — continuity without creating a new session
- Proactive chat triggers: configure rules to automatically open the chat widget or display a message when a visitor meets certain conditions (e.g. been on pricing page > 30 seconds, has visited 3+ pages, is on the checkout page)
- CSAT rating: after a chat ends, the visitor is prompted to rate the conversation (thumbs up/down or 1–5 stars) — responses feed into the Support domain's CSAT reporting
- Widget embed code generator: Filament settings page displays the snippet with syntax highlighting, a copy-to-clipboard button, and a preview mode that renders the widget in an iframe within the Filament UI

### AI-Powered
- First response generation: GPT-4o with knowledge base context generates the AI first response — the system prompt instructs the AI to answer concisely and suggest the human agent for complex issues; the visitor is never told it is an AI unless specifically asked
- Intent classification: the AI classifies the visitor's intent (sales inquiry / support / billing / other) on the first message and routes to the appropriate Omnichannel Inbox queue — a billing inquiry goes to the billing queue, a sales inquiry to the sales queue
- Conversation summary: when a chat session ends, GPT-4o generates a 2–3 sentence summary of the conversation and stores it in the inbox conversation record — the agent can add notes before the conversation is closed

## Data Model

Live Chat Widget shares the Omnichannel Inbox data model — it does not define its own conversation or message tables. All chat data is stored as `inbox_conversations` and `inbox_messages` with `channel_type = 'live_chat'`. The only module-specific table is widget configuration:

```erDiagram
    comms_live_chat_settings {
        ulid id PK
        ulid company_id FK
        string primary_color
        string background_color
        enum position
        string launcher_icon_url "nullable"
        string greeting_message
        string offline_message
        boolean ai_first_response_enabled
        boolean proactive_triggers_enabled
        json proactive_trigger_rules "nullable"
        boolean csat_enabled
        timestamps created_at/updated_at
    }

    comms_widget_visitor_sessions {
        ulid id PK
        ulid company_id FK
        string visitor_uuid
        ulid contact_id FK "nullable"
        string current_page_url
        json pages_visited
        integer time_on_site_seconds
        timestamp started_at
        timestamp last_seen_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `comms_live_chat_settings.position` | enum: `bottom_right` / `bottom_left` |
| `comms_widget_visitor_sessions.visitor_uuid` | Anonymous identifier from `localStorage`; linked to `contact_id` after JWT identification |
| `comms_widget_visitor_sessions.pages_visited` | JSON array of `{url, title, visited_at}` — capped at 50 entries; updated via WebSocket events |
| `comms_live_chat_settings.proactive_trigger_rules` | JSON array of trigger definitions: `{condition_type, threshold, delay_seconds, message}` |
| Channel data | `inbox_conversations.channel_type = 'live_chat'` — all conversation/message records are in the Omnichannel Inbox (Support/Comms domain) tables |

## Permissions

```
comms.live-chat.manage-settings
comms.live-chat.view-chats
comms.live-chat.handle-chats
comms.live-chat.view-analytics
comms.live-chat.configure-ai
```

## Filament

- **Custom settings page:** `LiveChatSettingsPage` — widget customisation form with live preview pane (the widget rendered in an iframe using current settings), the embed script snippet with copy button, and tabs for: Appearance, Offline Mode, AI Settings, Proactive Triggers
- **Custom agent page:** `LiveChatAgentPage` — real-time active chat sessions for agents: a split-pane view with the conversation list on the left and the active chat on the right, visitor context panel (pages viewed, CRM contact, open tickets) on the right sidebar, AI suggested response shown above the message input
- **No standard CRUD resource** — widget settings are a single settings record per company, not a list of resources
- **Widget:** `ActiveChatsWidget` on comms dashboard — count of active chat sessions, waiting sessions (no agent), and today's resolved chats; clicking navigates to the agent page
- **Nav group:** Internal (comms panel) — though the widget itself is external (customer-facing), the management interface lives in Internal comms alongside messaging

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Intercom chat widget | Embeddable chat, AI first response, visitor identification |
| Crisp | Live chat widget, offline mode, CSAT rating |
| Tidio | AI-powered live chat with automation |
| LiveChat | Agent chat platform with canned responses and routing |
| Drift (now Salesloft) | Conversational marketing chat with proactive triggers |

## Related

- [[messaging]]
- [[notification-center]]
- [[../support/INDEX]]
- [[../crm/contacts]]
- [[../crm/appointment-scheduling]]

## Implementation Notes

### WebSocket Architecture
The widget client JS connects to the Reverb server on the `presence-chat-widget.{company_id}` channel. This channel is a presence channel — only authenticated agents can be "present"; anonymous visitors connect to a private channel keyed to their session. The widget JS bundle is served from Cloudflare CDN at `//cdn.flowflex.com/widget/v1/widget.js` — versioned with a content hash to enable long cache TTLs.

The WebSocket connection is established lazily — only when the visitor opens the chat widget, not on page load — to avoid unnecessary connections from visitors who never open chat.

### Widget JS Bundle
The widget bundle must be framework-agnostic (no React or Vue dependency) and under 30KB gzipped. It uses vanilla JS with a Web Components shadow DOM approach to isolate widget styles from the host page's CSS. The widget initialises via `window.FlowFlexWidget = new FlowFlexWidget({apiKey: '...', ...})` where `apiKey` is the company's public widget key (not a secret — it identifies the company, not a user).

### Content Security Policy
Host websites with strict CSP may block the widget's WebSocket connection or CDN script load. The settings page must document the required CSP directives: `connect-src wss://ws.flowflex.com; script-src https://cdn.flowflex.com`. These are shown in the embed snippet documentation tab.

### Proactive Triggers
Proactive triggers are evaluated client-side in the widget JS (not server-side) to avoid latency. The trigger rules are serialised into the widget configuration JSON served from the FlowFlex API on widget initialisation. Supported condition types: `time_on_page` (seconds on current page), `page_url_contains` (URL match), `visit_count` (pages visited in session), `exit_intent` (mouse moves toward top of browser). Each trigger fires at most once per session to avoid annoyance.
