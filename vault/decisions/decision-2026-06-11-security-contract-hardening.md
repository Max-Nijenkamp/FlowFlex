---
type: adr
date: 2026-06-11
status: decided
domain: All
color: "#F97316"
---

# Security Contract Hardening — Mandatory canAccess(), Webhook Verification, Guest Guards, Encrypted PII

## Context

A spec-conformance audit (2026-06-11) of all 173 module specs (see [[build/security-audit-2026-06-11]]) found **184 HIGH / 85 MEDIUM / 29 LOW** findings. The failures were not architectural — the baseline in [[architecture/security]] and [[architecture/auth-rbac]] is sound — but systemic **under-specification** of the security contract in the specs themselves. Five repeatable clusters accounted for nearly all HIGH findings:

1. **Missing `canAccess()`** on Filament resources/pages — ~100+ HIGH across all 31 domains. Custom pages are the worst case (Filament does not auto-gate them).
2. **Unauthenticated public/external surfaces** with no declared guest/portal guard boundary.
3. **Sensitive PII / secrets** stored without `encrypted-fields` declared.
4. **Inbound webhooks** with signature verification marked `*(assumed)*` rather than required.
5. **No rate limiter** on expensive/abuse-prone surfaces (exports, webhooks, public token endpoints).

The spec template ([[_meta/spec-template]]) is **frozen** — changing it requires an ADR + backfill. This ADR authorises those changes.

## Options Considered

1. **File 184 individual gaps, fix per-spec, leave template as-is.** Rejected — treats a systemic template gap as 184 one-offs; the same omission recurs on every future spec.
2. **Codify the contract in the frozen template + filament-patterns, backfill via audit report.** Chosen — single highest-leverage fix; makes the omission impossible to repeat because the skeleton now demands it.
3. **Overturn the all-Filament UI ADR** ([[build/decisions/decision-2026-06-10-all-filament-hybrid-ui]]) in response to the "shouldn't feel like Filament" request. Rejected — the request resolved to theming-only; the all-Filament decision stands. UI-THEME findings are LOW (branding polish), not security.

## Decision

1. **`canAccess()` is mandatory and explicit in every spec's `## Filament` section.** The skeleton gains a required `canAccess()` contract line: every resource/page gates on `permission + BillingService::hasModule(module-key)`. Custom pages must state it explicitly (no auto-gating). Enforced via [[architecture/filament-patterns]] #1.
2. **Inbound webhooks require signature verification as a stated requirement**, never `*(assumed)*`. The spec must name the verification mechanism and secret source.
3. **Public / portal / unauthenticated surfaces must declare their guard boundary** (guest guard or scoped portal guard) plus signed/single-use token semantics where applicable.
4. **Sensitive PII and secrets must be declared in `encrypted-fields`** and flagged 🔐 in the Data Model — restating the existing encryption convention as an audit-enforced gate. Encrypted columns use `text`; queryable encrypted fields (e.g. unique email) get a deterministic `*_hash` companion column.
5. **Rate limiters must be cited** on exports, bulk ops, public token endpoints, and webhooks.

The audit report [[build/security-audit-2026-06-11]] is the backfill worklist — the per-spec HIGH/MEDIUM items are tracked as 7 systemic gaps (see [[build/gaps/INDEX]]) rather than 184 individual gap files.

## Consequences

- **Template changed** (frozen → amended by this ADR): [[_meta/spec-template]] `## Filament` skeleton + conventions. [[architecture/filament-patterns]] #1 strengthened to require spec-level declaration.
- **Backfill is incremental**: specs are hardened at `/flowflex:start` time per module (the briefing now surfaces the contract), not in one mass rewrite. The 7 encrypted-fields HIGH items were fixed immediately (this session).
- Future specs cannot omit the contract — the mandatory skeleton line + filament-patterns #1 catch it at review.
- No change to the all-Filament hybrid UI decision; theming remains a separate LOW-priority polish track.

## Related

- [[build/security-audit-2026-06-11]]
- [[architecture/security]]
- [[architecture/auth-rbac]]
- [[architecture/filament-patterns]]
- [[_meta/spec-template]]
- [[build/decisions/decision-2026-06-10-all-filament-hybrid-ui]]
