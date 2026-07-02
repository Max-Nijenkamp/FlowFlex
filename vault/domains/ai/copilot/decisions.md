---
domain: ai
module: copilot
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Copilot — Decisions

---

## Tools are the only data path (no free-form queries)

The model never receives raw table/SQL access. Every data read goes through a registered `ToolDefinition` that wraps an existing domain service, runs a per-tool permission check, and is `CompanyScope`-bound. Rationale: this makes tenant isolation, authorisation, and module gating enforceable in one place and keeps an LLM from ever composing an unbounded query ([[../../../security/data-ownership]]).

---

## Copilot is a pure consumer — it writes only its own tables

Copilot owns `ai_copilot_conversations` + `ai_copilot_messages` and nothing else. Cross-domain access is read-only via services; any future "copilot does X in domain Y" must go through that domain's events, never a direct write. This keeps data ownership unambiguous.

---

## All LLM calls through LlmGateway

Copilot never calls a provider SDK directly — it uses [[../model-config/api|ai.config]]'s `LlmGateway::complete('copilot', …)`. This centralises budget enforcement, feature toggles, usage metering, cost accounting, and provider fallback (mirrors ai.config's decision).

---

## Conversations private to their owning user

A second privacy layer on top of `CompanyScope`: a `user_id` filter means colleagues in the same company cannot read each other's copilot threads. Rationale: chat history often contains draft/exploratory content the author would not expect to be company-visible.

---

## `ai_copilot_config` table dropped; provider config lives in ai.config

The v1 spec's separate `ai_copilot_config` table is dropped *(assumed)* — provider/model/budget settings live in `ai_config` and are consumed via `LlmGateway`. Avoids duplicated, drifting provider config.

---

## Prompt-injection: data-only wrapping over model trust

Rather than trusting the model to ignore hostile instructions, tool results are structurally wrapped as data-only and the system prompt asserts distrust; output renders as plain text. A best-effort test fixture guards the behaviour *(assumed)*. Rationale: defence-in-depth for a feature that feeds untrusted record content to an LLM.
