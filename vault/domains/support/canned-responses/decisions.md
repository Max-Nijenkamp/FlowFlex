---
domain: support
module: canned-responses
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Canned Responses — Local Decisions

## Decided

- **Action, not a service.** Single operation (render + increment usage) → `lorisleiva/laravel-actions` `RenderCannedResponseAction`, no interface/service pair.
- **String substitution only.** Placeholders are a fixed known token set replaced literally; unknown tokens left as-is *(assumed)* — never a templating engine (no code eval, XSS-safe).
- **Personal vs shared on one table.** `is_shared` + `owner_id` on `sup_canned_responses`, no separate sharing table.

## Assumed (overridable via ADR)

- Unknown placeholders left literal *(assumed)*.
- Chat composer insertion deferred to P3 *(soft-dep)*.

## Related

- [[./unknowns]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
