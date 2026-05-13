---
type: module
domain: AI & Automation
panel: ai
module-key: ai.chatbot
status: planned
color: "#4ADE80"
---

# Chatbot

> Configurable AI chatbot for customer-facing use — trained on a company knowledge base, with human handoff and full conversation logging.

**Panel:** `ai`
**Module key:** `ai.chatbot`

---

## What It Does

Chatbot provides a customer-facing AI assistant that companies can deploy on their website or customer portal. Administrators configure the chatbot's persona, connect it to a company knowledge base (FAQ documents, product documentation, support articles), and define when it should escalate to a human agent. Every conversation is logged, and unanswered questions are surfaced for knowledge base improvement. The chatbot embeds via a JavaScript snippet and can also be integrated with the FlowFlex support module for ticket creation on handoff.

---

## Features

### Core
- Knowledge base connection: link documents, FAQs, and web pages as the chatbot's information source
- Persona configuration: name, avatar, tone (formal/casual/friendly), and language
- Widget embed: copy/paste JavaScript snippet for website integration
- Human handoff: detect when the chatbot cannot answer and route to a live agent or create a support ticket
- Conversation logging: full transcript of every chat session stored against the company
- Unanswered question report: list of questions the chatbot could not answer for knowledge base improvement

### Advanced
- Multi-language support: detect visitor language and respond accordingly
- Working hours: restrict live handoff to business hours; offer ticket creation outside hours
- Pre-chat form: collect visitor name, email, or company before the conversation starts
- Proactive messages: trigger a greeting message after a visitor spends X seconds on a page
- Chat rating: end-of-conversation satisfaction rating (thumbs up/down, 1–5 stars)

### AI-Powered
- Retrieval-augmented generation: answers grounded in company documents, not hallucinated
- Intent detection: classify conversation intent and route to the most relevant knowledge base section
- Continuous learning: low-rated conversations flagged for knowledge base review and improvement

---

## Data Model

```erDiagram
    chatbot_configs {
        ulid id PK
        ulid company_id FK
        string name
        string persona_tone
        string avatar_url
        json knowledge_source_ids
        boolean handoff_enabled
        boolean outside_hours_ticket
        timestamps created_at_updated_at
    }

    chatbot_conversations {
        ulid id PK
        ulid config_id FK
        ulid company_id FK
        string visitor_identifier
        string visitor_email
        string language
        string status
        integer satisfaction_rating
        timestamp started_at
        timestamp ended_at
    }

    chatbot_messages {
        ulid id PK
        ulid conversation_id FK
        string role
        text content
        boolean was_handoff_trigger
        timestamp created_at
    }

    chatbot_configs ||--o{ chatbot_conversations : "runs"
    chatbot_conversations ||--o{ chatbot_messages : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `chatbot_configs` | Chatbot setup | `id`, `company_id`, `name`, `persona_tone`, `handoff_enabled` |
| `chatbot_conversations` | Chat sessions | `id`, `config_id`, `visitor_email`, `status`, `satisfaction_rating` |
| `chatbot_messages` | Chat messages | `id`, `conversation_id`, `role`, `content`, `was_handoff_trigger` |

---

## Permissions

```
ai.chatbot.configure
ai.chatbot.view-conversations
ai.chatbot.manage-knowledge-base
ai.chatbot.view-analytics
ai.chatbot.export
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\ChatbotConfigResource`
- **Pages:** `ListChatbotConfigs`, `CreateChatbotConfig`, `EditChatbotConfig`
- **Custom pages:** `ConversationLogPage`, `UnansweredQuestionsPage`, `EmbedSnippetPage`
- **Widgets:** `ConversationVolumeWidget`, `SatisfactionScoreWidget`
- **Nav group:** Assistant

---

## Displaces

| Feature | FlowFlex | Intercom | Drift | Tidio |
|---|---|---|---|---|
| Knowledge base grounding | Yes | Yes | Yes | Yes |
| Human handoff | Yes | Yes | Yes | Yes |
| Native support ticket creation | Yes | Yes | No | No |
| RAG-based answers | Yes | Partial | Partial | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**External dependency — AI provider and RAG:** The chatbot uses Retrieval-Augmented Generation. The pipeline is:
1. Customer message arrives → embed the message text using **OpenAI `text-embedding-3-small`** API.
2. Search the company's knowledge base index in **Meilisearch** (configured with semantic search via the `embedder` setting) for the top 5 most relevant document chunks.
3. Send the retrieved chunks + conversation history to **Claude claude-sonnet-4-6** (Anthropic Claude API) with a system prompt defining the persona and response constraints.
4. Stream the response back to the visitor.

**Widget embed architecture:** The chatbot widget is a JavaScript bundle (`chatbot.js`) served from FlowFlex's public CDN (S3/R2 + Cloudflare). It is loaded via the company's embed snippet `<script src="https://cdn.flowflex.com/chatbot/v1/{company_id}.js">`. The JS bundle creates a shadow DOM widget on the host website — isolated from the host page's CSS. Visitor messages are sent to a public API endpoint `POST /api/v1/chatbot/{config_id}/message`. This endpoint is NOT a Filament route — it is a public API controller with rate limiting (20 messages per visitor per hour via Redis).

**Human handoff:** When `was_handoff_trigger = true`, the chatbot conversation is converted to a support ticket. A `ChatbotHandoffJob` is dispatched that creates a ticket in the `it.service-desk` module (if active) via the `TicketService`. The visitor is shown a "We're connecting you with a human agent" message. If outside business hours (`chatbot_configs.outside_hours_ticket = true`), a ticket is created with status `waiting` instead of dispatching a live agent.

**Knowledge base indexing:** When a document is added to `chatbot_configs.knowledge_source_ids`, a `IndexChatbotKnowledgeJob` job is dispatched. It fetches the document, splits it into chunks (500 tokens with 50-token overlap), embeds each chunk via OpenAI, and indexes them in Meilisearch under the company's chatbot index namespace (`chatbot_{company_id}`). Chunk metadata stores the source document ID and title for citation display.

**Filament:** `ConversationLogPage`, `UnansweredQuestionsPage`, and `EmbedSnippetPage` are custom `Page` classes. `ConversationLogPage` renders a two-panel view (conversation list + message thread) — similar in architecture to `MessagingPage`. `EmbedSnippetPage` shows a syntax-highlighted code snippet and a live preview iframe of the widget on a blank page.

**GDPR:** `chatbot_conversations` and `chatbot_messages` may contain visitor PII (email, name, message content). On GDPR erasure, anonymise: set `visitor_email` to `null`, set `visitor_identifier` to `[deleted]`, replace `chatbot_messages.content` with `[message deleted]`.

## Related

- [[copilot]] — internal equivalent for FlowFlex users
- [[document-intelligence]] — knowledge base documents can be AI-processed
- [[sentiment-analysis]] — chatbot transcripts can be sentiment-analysed
- [[workflow-builder]] — handoff events can trigger support workflows
