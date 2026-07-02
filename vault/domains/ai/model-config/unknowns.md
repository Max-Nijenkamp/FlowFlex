---
domain: ai
module: model-config
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Model Configuration — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR.

---

## Open Questions

1. **BYO-key vs. platform-key.** v1 assumes customers bring their own provider key. When does the platform-provided key + usage billing arrive, and how is per-company metering reconciled with [[../../../product/pricing-model]]?
2. **Cost source of truth.** `cost_cents` is computed from a provider pricing table — where does that table live, and how is it kept current as provider prices change?
3. **Fallback semantics.** On primary-provider failure, is fallback same-provider-different-model, or cross-provider? Does a fallback call count against the same budget?
4. **Usage-log retention.** 12-month prune is assumed — confirm against GDPR/retention policy ([[../../../architecture/data-lifecycle]]).
5. **Data-residency enforcement.** Does `data_residency: eu` hard-block non-EU providers/models at save time, or only prefer them?

---

## Assumed Items (unverified)

- `*(assumed)*` — Anthropic Claude is the default provider.
- `*(assumed)*` — BYO-key in v1; platform-key + usage billing deferred.
- `*(assumed)*` — `ai_usage_log` pruned at 12 months.
- `*(assumed)*` — the v1 spec's separate `ai_copilot_config` table was dropped; provider config lives here in `ai_config`.

> [!warning] UNVERIFIED
> The provider pricing table (for `cost_cents`) has no defined source, update cadence, or owner. Confirm before relying on cost figures for billing.
