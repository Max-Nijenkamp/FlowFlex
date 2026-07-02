---
domain: ai
module: copilot
feature: chat-console
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Chat Console

The copilot chat surface: a streaming conversation with the AI assistant plus a sidebar of the user's own private conversation history. This is where questions are asked, tools are invoked, and answers stream back.

## Behaviour

- User sends a message → `CopilotService::send` runs the agent loop (build tool set → `LlmGateway::complete` → execute permitted tools → final answer), persisting user/assistant/tool turns.
- Conversations are **private to their owning user** — a second-layer `user_id` filter over `CompanyScope`; other users in the same company never see them.
- Assistant response streams token-by-token *(assumed: SSE)*.
- Each message is metered via `LlmGateway`; a budget hard-stop surfaces a friendly error rather than a stack trace.
- Panel/record context can be passed as structured metadata (e.g. "summarise this deal").

## UI

- **Kind**: custom-page   <!-- #8-style chat -->
- **Page**: "Copilot" (`/app/ai/copilot`) *(route slug assumed)*
- **Layout**: left rail = conversation list (new-chat button, per-conversation titles); main pane = streaming message list; bottom = composer input. Tool calls render inline as collapsible "used CRM metrics" cards.
- **Key interactions**: type + send → optimistic user bubble → streaming assistant reply; click a conversation → load its history; new chat → fresh thread; a denied tool shows "not permitted" inline rather than failing the turn.
- **States**: empty (no conversations → "Ask me anything about your company" prompt + example chips) · loading (assistant typing indicator / streaming) · error (budget exceeded or provider error → toast + retry; rate-limited → "slow down" notice) · selected (active conversation highlighted in the rail).
- **Gating**: `ai.copilot.use` + `hasModule('ai.copilot')`; per-tool domain permissions checked at execution. A per-user/per-company rate limiter throttles sends.

## Data

- Owns / writes: `ai_copilot_conversations`, `ai_copilot_messages` (this module's own tables).
- Reads: other domains **read-only** via registered tools (see [[tool-registry|Tool Registry]]); `LlmGateway` from [[../../model-config/_module|ai.config]].
- Cross-domain writes: none — copilot never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `LlmGateway::complete` from `ai.config`.
- Uses: [[tool-registry|Tool Registry]] for every data read; [[draft-and-summarise|Draft & Summarise]] modes live in this same surface.
- Shared entity: `user_id` (platform `users`), read-only.

## Unknowns

> [!warning] UNVERIFIED
> Streaming transport (SSE vs Livewire polling vs Reverb) and the `/app/ai/copilot` route slug are both assumed and high-impact — they shape the page + any internal streaming route. See [[../unknowns]].

## Related

- [[../_module|AI Copilot]] · [[tool-registry|Tool Registry]] · [[draft-and-summarise|Draft & Summarise]]
- [[../security]] · [[../../../../architecture/patterns/custom-pages]]
