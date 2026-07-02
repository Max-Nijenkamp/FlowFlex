---
domain: ai
module: copilot
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Copilot — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR.

---

## Open Questions

1. **Streaming transport.** Assistant responses are assumed streamed via SSE. Confirm against [[../../../architecture/websockets]] — SSE, Livewire polling, or Reverb? Affects the chat page + any internal streaming route.
2. **Hosting route/panel.** The chat page route is assumed `/app/ai/copilot` (ai resources hosted in `/app`). Confirm the actual panel slug for `ai` artifacts.
3. **v1 tool set.** Assumed: crm deal metrics, finance revenue/invoice metrics, hr headcount, support ticket lookup, record summarisation. Confirm the exact shipping set and each tool's declared permission.
4. **Draft/summarise as modes vs. separate surface.** Assumed to be modes/actions within the chat console rather than a separate page. Confirm the UX.
5. **Prompt-injection test rigour.** The "instruction embedded in tool result not followed" assertion is best-effort against a non-deterministic model. Define what a passing test looks like.
6. **Conversation retention.** No prune job assumed; conversations rely on soft-delete / user action. Confirm against [[../../../architecture/data-lifecycle]].

---

## Assumed Items (unverified)

- `*(assumed)*` — assistant responses streamed via SSE.
- `*(assumed)*` — chat page route `/app/ai/copilot`.
- `*(assumed)*` — v1 tool set as listed above.
- `*(assumed)*` — draft & summarise are modes within the chat console, not a separate page.
- `*(assumed)*` — `title` auto-derived from the first message.
- `*(assumed)*` — messages cascade-delete with the conversation (no individual soft-delete).
- `*(assumed)*` — the v1 `ai_copilot_config` table was dropped; provider config lives in `ai_config`.
- `*(assumed)*` — prompt-injection test is best-effort.

> [!warning] UNVERIFIED
> The streaming transport, the hosting route/panel slug, and the exact v1 tool set are the three highest-impact assumptions. Resolve before build — they shape the chat page, an internal streaming route, and the tool registry.
