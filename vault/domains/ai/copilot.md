---
type: module
domain: AI & Automation
panel: ai
module-key: ai.copilot
status: planned
color: "#4ADE80"
---

# Copilot

> Context-aware AI assistant embedded in every FlowFlex panel — answers questions, suggests actions, and drafts content based on the current workspace context.

**Panel:** `ai`
**Module key:** `ai.copilot`

---

## What It Does

Copilot is a persistent AI assistant available as a slide-out panel throughout the entire FlowFlex application. It understands the context of the screen the user is on — if they are viewing a CRM contact, Copilot can summarise that contact's history; if they are drafting an invoice, Copilot can suggest line items. Users can also ask free-form questions about their business data, request drafts of communications, or ask Copilot to explain a particular metric they are viewing. All interactions are logged per user for auditability.

---

## Features

### Core
- Persistent chat panel: accessible from every Filament panel via a keyboard shortcut or floating button
- Context injection: current page entity data (record ID, type, key fields) automatically included in AI context
- Free-form question answering: query business data in natural language ("How many open CRM deals do I have this month?")
- Draft generation: generate email drafts, summary notes, and document templates
- Action suggestions: Copilot suggests relevant next actions based on the current screen
- Conversation history: per-user chat history retained for the session

### Advanced
- Cross-panel context: Copilot can pull data across panels (e.g. "Summarise this customer's support and billing history")
- Saved prompts: users can save frequently used prompt templates for one-click reuse
- Admin prompt library: organisation-wide curated prompt templates for common tasks
- Output actions: Copilot output can be inserted directly into a Filament form field
- Role-aware responses: answers respect the user's permission scope — no data returned that the user cannot access

### AI-Powered
- Intent routing: Copilot detects whether the user wants information, a draft, an action, or analysis and routes accordingly
- Streaming responses: real-time token streaming for fast perceived response time
- Citation linking: answers reference the source records with clickable links

---

## Data Model

```erDiagram
    copilot_conversations {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string panel_context
        string entity_type
        ulid entity_id FK
        timestamp started_at
        timestamps created_at_updated_at
    }

    copilot_messages {
        ulid id PK
        ulid conversation_id FK
        string role
        text content
        json citations
        integer tokens_used
        timestamp created_at
    }

    copilot_conversations ||--o{ copilot_messages : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `copilot_conversations` | Conversation sessions | `id`, `company_id`, `user_id`, `panel_context`, `entity_type`, `entity_id` |
| `copilot_messages` | Individual messages | `id`, `conversation_id`, `role`, `content`, `citations`, `tokens_used` |

---

## Permissions

```
ai.copilot.use
ai.copilot.view-history
ai.copilot.manage-prompt-library
ai.copilot.view-usage-stats
ai.copilot.configure
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\CopilotConversationResource` (admin view of logs)
- **Pages:** `CopilotHistoryPage`, `PromptLibraryPage`
- **Custom pages:** `CopilotPanelComponent` (embedded slide-out in every panel)
- **Widgets:** `CopilotUsageWidget`, `TokenConsumptionWidget`
- **Nav group:** Assistant

---

## Displaces

| Feature | FlowFlex | Zapier AI | Custom GPT | Intercom Fin |
|---|---|---|---|---|
| Business context awareness | Yes | No | No | Partial |
| Cross-panel data querying | Yes | No | No | No |
| Permission-scoped responses | Yes | No | No | No |
| Action suggestions | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**External dependency — AI provider:** All Copilot responses use the **Anthropic Claude API** (claude-sonnet-4-6 or claude-opus as configured). The service class `app/Services/AI/CopilotService.php` wraps `Anthropic\SDK\Anthropic` (add `anthropic-ai/sdk` PHP package to `composer.json`). Prompt caching should be enabled on the system prompt (which includes the user's permission set and company context) to reduce token costs on repeated queries within a session.

**Context injection mechanism:** When the Copilot panel opens, the Livewire component calls `CopilotContextService::buildContext($panel, $entityType, $entityId)`. This service fetches the key fields of the current entity (e.g. a CRM deal: title, value, stage, contact, last 5 activities) and serialises them to a compact JSON context string injected as a `<context>` block in the user message. The service respects the user's permissions — it does not fetch fields the user cannot read.

**Streaming responses:** Use Anthropic's streaming API (`$client->messages()->createStreamed(...)`). The Livewire component receives the stream via a `StreamedResponseEvent` broadcast on the user's private Reverb channel `copilot.{user_id}`. Each token chunk is appended to the displayed message text via Alpine.js. This requires Reverb — it is the only way to stream tokens to a Livewire component without polling.

**Real-time:** Reverb required for streaming responses only. The channel is `private-copilot.{user_id}`. If Reverb is not available, fall back to non-streamed responses (wait for the full response then display).

**`CopilotPanelComponent`:** Registered as a global Livewire component mounted in the Filament panel layout Blade file. It is a slide-out drawer rendered over the main content area. The component is persistent across page navigations within the panel (Livewire's `wire:navigate` preserves it).

**Role-aware responses:** Before querying the Copilot API, the system prompt includes a list of the user's active permissions (`$user->getAllPermissions()->pluck('name')->toArray()`). The LLM is instructed not to return information about domains the user lacks permission to access. Additionally, the context injection service enforces this at the data-fetching layer — it only includes entity data from permitted domains.

**Token tracking:** `copilot_messages.tokens_used` is populated from the API response `usage.input_tokens + usage.output_tokens`. A monthly token budget per company is enforced: `CopilotBudgetService::checkBudget($company)` runs before each request. If the company's monthly token spend exceeds the plan limit, a quota exceeded message is returned without an API call.

## Related

- [[workflow-builder]] — Copilot can trigger workflows from suggestions
- [[document-intelligence]] — Copilot surfaces document summaries
- [[chatbot]] — customer-facing equivalent of Copilot
- [[ai/INDEX]] — AI domain overview
