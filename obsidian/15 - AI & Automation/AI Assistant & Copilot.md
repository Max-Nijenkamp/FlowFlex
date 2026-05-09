---
tags: [flowflex, domain/ai-automation, ai-assistant, copilot, phase/6]
domain: AI & Automation
panel: ai
color: "#06B6D4"
status: planned
last_updated: 2026-05-08
---

# AI Assistant & Copilot

A chat-based AI assistant with full access to your FlowFlex data. Ask it anything about your business, have it draft things, run actions, and surface insights — without writing a single prompt template yourself.

**Who uses it:** All users
**Filament Panel:** All panels (global sidebar widget)
**Depends on:** Core, all active domain modules, [[AI Infrastructure]]
**Phase:** 6
**Build complexity:** Very High — 1 resource, 2 pages, 4 tables, streaming UI

---

## What It Can Do

### Ask Questions (Read)

- "How many employees are on leave this week?"
- "What's our cash position across all accounts right now?"
- "Show me all open deals over €50,000 assigned to Anna"
- "Which tasks are overdue for Project Atlas?"
- "What was our revenue last month vs the month before?"
- "Who hasn't completed their mandatory compliance training?"
- "What's the average time to hire for engineering roles this year?"

The assistant queries the FlowFlex data layer directly — no hallucination about your business data, always current.

### Take Actions (Write — with confirmation)

- "Create a task: follow up with Acme Corp by Friday, assign to me"
- "Send the Q2 invoice to john@acmecorp.com"
- "Book a meeting with the HR team for next Tuesday at 2pm"
- "Update the status of Deal #1247 to Negotiation"
- "Archive all tasks in the Marketing project that have been done for more than 30 days"
- All write actions show a confirmation preview before executing

### Draft Content

- "Write a rejection email for candidate Sarah Johnson — keep it warm and professional"
- "Draft an onboarding welcome message for our new engineer starting Monday"
- "Generate a performance review template for a Sales Manager role"
- "Write an internal announcement about the new expense policy"
- "Summarise this contract in plain English" (file upload)
- "Create a project brief for the website redesign"

### Summarise & Analyse

- "Summarise the last 10 support tickets from Acme Corp"
- "What are the main themes in this quarter's employee feedback survey?"
- "Analyse our sales pipeline and tell me what's most at risk"
- "What's trending in our support tickets this month?"
- "Give me a health check on the Marketing department — headcount, active projects, budget status"

### Surface Proactive Insights

The assistant proactively surfaces things worth knowing:
- "3 invoices became overdue overnight"
- "Employee leave balance for Q4 review is due in 5 days for 14 employees"
- "Deal close rate dropped 12% week-over-week — here's what changed"
- "Koen hasn't logged time in 5 days — they have 3 active tasks"

---

## Interface

### Command Bar (⌘K / Ctrl+K)

- Global keyboard shortcut opens command bar from anywhere in the app
- Type to search records, run actions, or start a conversation
- Recent actions shown below search input
- AI suggestions appear as user types

### Sidebar Chat Panel

- Persistent chat panel in right sidebar (toggle with button)
- Full conversation history per user
- Conversation context: "you're viewing project Atlas" — assistant knows what's on screen
- Can reference records by clicking them in the main view

### Inline Suggestions

- Text fields show AI suggestions inline (press Tab to accept)
- Email compose: subject line suggestions, reply suggestions
- Task description: auto-complete based on project context
- Report builder: "describe what you want to see" → auto-builds query

---

## Technical Architecture

- LLM routing via [[AI Infrastructure]] (OpenAI GPT-4o by default, local Ollama option)
- Tool-calling / function-calling for data access and write actions
- Each domain registers its "tools" (read functions, write functions) with the AI layer
- Retrieval-Augmented Generation (RAG) for knowledge base documents
- Streaming responses via Server-Sent Events
- Conversation history stored per-user (last 50 turns, then summarised)
- Context window management: automatic summarisation when history exceeds model limit

---

## Privacy & Safety

- AI does not train on customer data — queries are ephemeral
- Role-based filtering: assistant only accesses data the user is permitted to see via RBAC
- Write action confirmation flow — no silent writes
- AI action audit log (every action taken by AI is recorded with full context)
- Admin can disable specific action categories (e.g. "AI can read but not write")
- No PII sent to external LLM APIs — PII is masked before sending (configurable)

---

## Database Tables (4)

### `ai_conversations`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `title` | string nullable | auto-generated from first message |
| `context_panel` | string nullable | which panel was active when started |
| `context_record_type` | string nullable | e.g. `App\Models\Deal` |
| `context_record_id` | ulid nullable | |
| `message_count` | integer default 0 | |
| `last_message_at` | timestamp nullable | |

### `ai_messages`
| Column | Type | Notes |
|---|---|---|
| `conversation_id` | ulid FK | |
| `role` | enum | `user`, `assistant`, `system` |
| `content` | text | |
| `tool_calls` | json nullable | tool calls made by assistant |
| `tool_results` | json nullable | results returned to assistant |
| `model_used` | string nullable | e.g. `gpt-4o` |
| `tokens_input` | integer nullable | |
| `tokens_output` | integer nullable | |
| `duration_ms` | integer nullable | |

### `ai_actions_log`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | |
| `conversation_id` | ulid FK nullable | |
| `action_type` | string | e.g. `create_task`, `send_email` |
| `action_data` | json | what was done |
| `confirmed_by_user` | boolean | |
| `executed_at` | timestamp nullable | |

### `ai_suggestions_dismissed`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | |
| `suggestion_key` | string | unique key per suggestion type |
| `dismissed_at` | timestamp | |

---

## Permissions

```
ai.copilot.use
ai.copilot.take-actions
ai.copilot.view-history
ai.copilot.admin-settings
```

---

## Competitor Comparison

| Capability | FlowFlex Copilot | Microsoft Copilot | HubSpot AI | Notion AI |
|---|---|---|---|---|
| Queries your live business data | ✅ | ✅ (M365 only) | ✅ (HubSpot only) | ❌ (docs only) |
| Cross-domain context (HR + Finance + CRM) | ✅ | ❌ | ❌ | ❌ |
| Takes write actions with confirmation | ✅ | ✅ | partial | ❌ |
| Privacy: no training on customer data | ✅ | ✅ | ✅ | ✅ |
| RBAC-filtered responses | ✅ | ✅ | ✅ | ❌ |
| Inline field suggestions | ✅ | ✅ | partial | ✅ |
| No extra subscription cost | ✅ | ❌ (€30/user/mo) | ❌ (extra tier) | ❌ (extra cost) |

---

## Related

- [[AI Overview]]
- [[AI Infrastructure]]
- [[Workflow Automation Builder]]
- [[AI Agents]]
