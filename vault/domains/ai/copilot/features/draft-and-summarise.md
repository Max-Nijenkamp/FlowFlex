---
domain: ai
module: copilot
feature: draft-and-summarise
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Draft & Summarise

Generation and summarisation modes of the copilot: draft an email reply / product description / job posting, or summarise a document, ticket thread, or meeting notes. These are actions within the chat surface, not a separate page.

## Behaviour

- **Draft generation** — prompt the model to produce an email reply, product description, or job posting; output returned as editable text (rendered plain, never executed).
- **Summarisation** — content (document, ticket thread, meeting notes) is fetched **with a permission check at fetch time**, passed to the model, and summarised.
- Both run through `CopilotService::send` → `LlmGateway::complete`, metered like any other message.
- Content to summarise is provided via context metadata or a tool fetch — copilot never reads content the user isn't authorised to see.

## UI

- **Kind**: custom-page (shared with [[chat-console|Chat Console]])   <!-- modes/actions within the same chat surface, not a separate page -->
- **Page**: within "Copilot" (`/app/ai/copilot`) — invoked via prompt or quick-action chips ("Draft reply", "Summarise this record") *(assumed UX)*.
- **Layout**: same chat pane; generated draft appears as an assistant message with copy / insert actions; summaries appear inline with a source reference.
- **Key interactions**: click "Summarise this record" from a panel context → context passed → streamed summary; edit/copy the generated draft.
- **States**: empty (no draft yet) · loading (generating / streaming) · error (fetch denied → "you don't have access to that content"; budget exceeded → friendly stop) · selected (draft message focused with copy/insert).
- **Gating**: `ai.copilot.use` + `hasModule`; the content being summarised is gated by the **source domain's** view permission at fetch.

## Data

- Owns / writes: `ai_copilot_messages` (the generated/summarised turns) — this module's own table.
- Reads: source content via permission-checked fetch through the owning domain's service (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `LlmGateway::complete` from [[../../model-config/_module|ai.config]].
- Uses: [[tool-registry|Tool Registry]] to fetch summarisable content under permission.
- Shared entity: none new.

## Test Checklist

### Unit
- [ ] Generated draft + summary output is treated as plain text (never executed / raw HTML).

### Feature (Pest)
- [ ] Draft generation runs through `CopilotService::send` → `LlmGateway::complete` and is metered like any message (provider mocked).
- [ ] Summarisation fetches source content with a **permission check at fetch time**; content the user can't view is refused ("you don't have access to that content").
- [ ] Summarise turns write only `ai_copilot_messages` — no cross-domain write.

### Livewire
- [ ] "Draft reply" / "Summarise this record" quick actions are gated by `ai.copilot.use` + `hasModule`.
- [ ] A fetch-denied summarise shows the access error inline without failing the whole turn.

## Unknowns

> [!warning] UNVERIFIED
> Whether draft/summarise are truly modes within the chat console (assumed) or warrant their own quick-action surface is unconfirmed. The exact set of draftable artefact types is assumed. See [[../unknowns]].

## Related

- [[../_module|AI Copilot]] · [[chat-console|Chat Console]] · [[tool-registry|Tool Registry]]
