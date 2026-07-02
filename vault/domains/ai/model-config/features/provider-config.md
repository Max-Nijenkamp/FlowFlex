---
domain: ai
module: model-config
feature: provider-config
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Provider Config

The settings surface where a company chooses its LLM provider, models, API key, monthly budget, data residency, and which AI features are enabled. Everything the `LlmGateway` needs to resolve a call lives in the single `ai_config` row this feature writes.

## Behaviour

- One `ai_config` row per company (unique on `company_id`) — create-or-update, never duplicated.
- Provider in {anthropic *(assumed default)*, openai, azure}; `default_model` must be valid for the chosen provider.
- `feature_models` is a per-feature override map (copilot / document-intelligence / workflows → model).
- `api_key` is **write-only**: blank on load, verified with a live test call before save, encrypted at rest, never re-displayed. An empty submit keeps the stored key.
- `monthly_token_budget` (nullable → no cap) and `data_residency` (eu / global) are cost/compliance controls.
- `enabled_features[]` toggles individual AI features; a disabled feature is refused by `LlmGateway` before any provider call.

## UI

- **Kind**: custom-page   <!-- #7 form page -->
- **Page**: "AI Model Configuration" (`/ai` → Settings → AI Model Configuration) *(route slug assumed)*
- **Layout**: single settings form — provider select, default-model select (options depend on provider), per-feature model overrides, write-only API-key field, monthly budget, data-residency toggle, feature checkboxes.
- **Key interactions**: pick provider → model options refresh; save → API key test-call validates before persist; key field shows "•••• set" placeholder, never the value.
- **States**: empty (no config yet → defaults pre-filled, key blank) · loading (saving/verifying key spinner) · error (invalid key → inline "key rejected by provider", validation errors per field) · selected (n/a — single form).
- **Gating**: `ai.config.manage`.

## Data

- Owns / writes: `ai_config` (this module's table only).
- Reads: active-module set from [[../../../core/billing-engine/_module|core.billing]] to gate the feature toggles.
- Cross-domain writes: none — settings are self-contained ([[../../../../security/data-ownership]]).

## Relations

- Feeds: the stored config is read by [[llm-gateway|LLM Gateway]] on every AI call.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] `ConfigureAiData` validation: provider enum, `default_model` valid for provider, `monthly_token_budget` min:0/nullable, `data_residency` enum.
- [ ] Write-only key rule: an empty `api_key` submit keeps the stored ciphertext (does not blank it).

### Feature (Pest)
- [ ] Create-or-update writes exactly one `ai_config` row per company (unique on `company_id`, never duplicated).
- [ ] API key verified with a live provider test call before persist; a rejected key aborts save with an inline error (provider mocked).
- [ ] Saved `api_key` is stored ciphertext and never returned to the form on reload.

### Livewire
- [ ] `AiConfigPage` `canAccess()` = `ai.config.manage` + `hasModule('ai.config')`; hidden otherwise.
- [ ] Provider select change refreshes the `default_model` options.
- [ ] Key field renders as write-only ("•••• set" placeholder), never exposing the stored value.

## Unknowns

> [!warning] UNVERIFIED
> The exact `/ai` Settings route slug, and whether `data_residency: eu` hard-blocks non-EU providers at save time or only prefers them. See [[../unknowns]].

- `*(assumed)*` Anthropic Claude is the default provider; BYO-key in v1.

## Related

- [[../_module|AI Model Configuration]] · [[llm-gateway|LLM Gateway]] · [[usage-dashboard|Usage Dashboard]]
- [[../security]] · [[../../../../architecture/patterns/encryption]]
