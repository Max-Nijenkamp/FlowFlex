---
domain: ai
module: model-config
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Model Configuration — Decisions

---

## LlmGateway is the sole LLM call path

Every AI feature routes LLM calls through `LlmGateway::complete`. No feature calls a provider SDK directly. This centralises budget enforcement, feature toggles, usage metering, cost accounting, and provider fallback in one place — and keeps the usage log unambiguous (single writer, per [[../../../security/data-ownership]]).

---

## BYO-key first; platform-key + usage billing later

v1 assumes each company brings its own provider API key *(assumed)*. A platform-provided key with usage-based billing is a later option (candidate for [[../../../product/pricing-model]]). This keeps v1 cost exposure on the customer and avoids building a billing-metering pipeline up front.

---

## Anthropic Claude as the default provider

Default provider is Anthropic Claude *(assumed)*, with OpenAI and Azure OpenAI as alternatives. Azure exists primarily to satisfy the EU `data_residency` option for GDPR-sensitive tenants.

---

## Budget hard-stop over soft warning

The monthly token budget is a **hard stop** (throws `AiBudgetExceededException`), not just an alert. A single 80% warning fires once per month (guarded by `budget_alerted_at`). Rationale: LLM spend is unbounded and per-token; a soft-only limit invites bill shock.

---

## Usage log append-only, pruned at 12 months

`ai_usage_log` is append-only (no updates/soft-deletes) so cost/usage history is tamper-evident, and pruned at 12 months *(assumed)* to bound table growth.
