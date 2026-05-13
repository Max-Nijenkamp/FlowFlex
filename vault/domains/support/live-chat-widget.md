---
type: module
domain: Support & Help Desk
panel: support
module-key: support.live-chat
status: planned
color: "#4ADE80"
---

# Live Chat Widget

> Embeddable shadow-DOM JS widget (~15 KB) for marketing sites and client portals, with Reverb real-time messaging, visitor identification, offline-to-ticket fallback, and AI-powered first response from the knowledge base.

**Panel:** `/support`
**Module key:** `support.live-chat`

## What It Does

Live Chat Widget provides a lightweight embeddable chat bubble that companies add to their marketing website or client portal via a single `<script>` tag. Visitors can start a conversation instantly — anonymous visitors are tracked by session ID, while authenticated users are identified via a signed JWT passed from the host page. Conversations are routed to the agent inbox in real time over Reverb WebSocket. When no agents are online, the widget switches to an offline form that creates a support ticket automatically so no conversation is ever lost. Before a human agent joins, an AI bot attempts to answer the visitor's question using knowledge base articles — reducing first response time and deflecting simple queries entirely.

## Features

### Core
- Embeddable JS snippet: single `<script src="https://app.flowflex.io/widget/chat.js" data-key="{key}">` tag. Widget renders in a shadow DOM so host page styles cannot bleed in.
- Widget bundle: ~15 KB gzipped, zero dependencies, loads asynchronously (non-blocking)
- Visitor identification: anonymous visitors tracked by UUID stored in `localStorage`. Authenticated visitors identified by signed JWT passed via `FlowFlexChat.identify({ name, email, userId })` JS method call from host page.
- Customisation: brand color, position (bottom-right / bottom-left), greeting message, agent avatar, widget title — all configured in Filament and embedded in the widget config payload
- Conversation routing: new chat session appears in the `LiveChatPage` in Filament, assigned to the first available online agent or to an auto-assign queue
- Offline mode: when no agents are marked online, widget shows an offline contact form instead of chat. Submission creates a `SupportTicket` automatically with channel = `chat`.
- Agent chat: Filament `LiveChatPage` shows all active sessions in a left panel, selected conversation thread in right panel. Agents mark themselves online/offline via a toggle.

### Advanced
- File attachment support in chat: visitors and agents can upload images and documents (via presigned S3 URL)
- Typing indicators: agent sees visitor typing via Reverb presence channel; visitor sees agent typing indicator
- Conversation rating: optional thumbs-up/thumbs-down at conversation close, stored as CSAT on the session record
- Chat transcript emailed to visitor on conversation end (configurable)
- Proactive chat triggers: show widget automatically after X seconds on page, or when visitor visits specific URL — configured in Filament
- Conversation history: returning identified visitors see their previous chat sessions in the widget

### AI-Powered
- AI first response: when a visitor sends their first message, Claude searches the knowledge base for relevant articles and sends an instant response with article links before routing to a human. Response is clearly labelled as AI-generated.
- Intent classification: AI classifies the visitor message intent (question / complaint / purchase / other) and sets the priority of the resulting session record, helping agents triage their queue
- Agent assist: while an agent is composing a reply, AI surfaces the top 2 knowledge base articles relevant to the current conversation thread as inline suggestions

## Data Model

```erDiagram
    support_chat_sessions {
        ulid id PK
        ulid company_id FK
        string visitor_id
        ulid contact_id FK
        ulid assignee_id FK
        string status
        float csat_score
        timestamp started_at
        timestamp ended_at
        ulid routed_to_ticket_id FK
        string channel_key
        json widget_config_snapshot
        timestamps created_at/updated_at
    }

    support_chat_messages {
        ulid id PK
        ulid session_id FK
        string sender_type
        ulid sender_id
        text body
        json attachments
        boolean is_ai_generated
        timestamp sent_at
    }

    support_chat_widget_configs {
        ulid id PK
        ulid company_id FK
        string channel_key
        string brand_color
        string position
        string greeting_message
        string widget_title
        boolean is_active
        json proactive_triggers
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `visitor_id` | UUID stored in visitor's `localStorage` — stable across page reloads, cleared on browser data wipe |
| `contact_id` | Populated when visitor is identified via JWT or manually matched to a CRM contact by email |
| `status` | active / waiting / closed / converted-to-ticket |
| `sender_type` | visitor / agent / ai / system |
| `is_ai_generated` | true = message sent by AI bot, displayed with AI label in widget UI |
| `routed_to_ticket_id` | Set when an offline session is converted to a support ticket |
| `channel_key` | Public key embedded in the script tag, used to look up widget config |

## Permissions

```
support.live-chat.view
support.live-chat.manage
support.live-chat.configure
support.live-chat.delete
support.live-chat.reports
```

## Filament

- **Resource:** None — the chat widget JS embed is a public asset, not a Filament Resource. Widget configuration is managed via `WidgetConfigResource`.
- **Custom pages:** `LiveChatPage` — full-screen custom Filament page (not a Resource) showing active sessions in left column, chat thread in right column. Agents mark themselves online here. Real-time updates via Reverb. Class: `App\Filament\Support\Pages\LiveChatPage`. `WidgetConfigResource` — standard CRUD resource for configuring widget appearance and proactive triggers.
- **Widgets:** `ActiveChatsWidget` on support panel dashboard showing count of active sessions and online agents
- **Nav group:** Inbox (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Intercom chat | Embeddable chat widget, bot first response |
| Crisp | Live chat, visitor tracking |
| Tidio | Chat widget, AI chatbot |
| Drift | Conversational marketing chat |
| Freshchat | Live chat with agent inbox |

## Related

- [[support-tickets]]
- [[knowledge-base]]
- [[domains/inbox/shared-inbox]]
- [[domains/crm/contacts]]

## Implementation Notes

- **Widget bundle:** Built as a separate Vite entry point — `resources/js/chat-widget/index.js`. Bundled independently from the main Filament/Inertia assets. Shadow DOM prevents CSS leakage. Widget communicates with the FlowFlex API via `fetch` (REST) for session create/message send and via Reverb WebSocket for real-time receive. No heavy framework — vanilla JS with minimal reactivity.
- **Reverb channels:** Each chat session occupies a private Reverb channel `chat-session.{session_ulid}`. Agents subscribe via `LiveChatPage` Livewire component. Visitors subscribe from the widget using a short-lived signed token returned from the `POST /api/chat/sessions` endpoint.
- **JWT identification:** Host page calls `FlowFlexChat.identify({ name, email, token })` where `token` is an HMAC-SHA256 signed JWT generated server-side by the host application using the company's widget secret key. The widget sends this token on session create; the API verifies signature and creates or updates the CRM contact.
- **Offline fallback:** Widget detects agent availability by calling `GET /api/chat/availability?key={channel_key}`. Returns `{ online: bool }`. Widget UI switches between live-chat and offline-form mode automatically. Offline form POSTs to `POST /api/chat/offline` which creates a `SupportTicket` directly.
- **AI first response:** On new session creation, a queued `SendAiFirstResponse` job fires. It calls the internal `KnowledgeBaseSearch` service to find relevant articles, then calls Claude via the AI domain's LLM gateway to generate a contextual reply. Response is saved as a `support_chat_message` with `is_ai_generated = true` and broadcast to the session channel. The job completes within ~3 seconds on typical knowledge base sizes.
