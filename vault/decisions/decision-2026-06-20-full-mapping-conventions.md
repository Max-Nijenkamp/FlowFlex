---
type: adr
date: 2026-06-20
status: decided
domain: All
color: "#F97316"
updated: 2026-06-20
---

# Full-mapping conventions (data ownership, RBAC, per-feature UI, cross-domain relations)

## Context

The whole vault is being fully mapped to a build-ready state — every domain exploded to the feature
level, every feature specifying its UI, cross-domain relations made explicit, and a hard data-ownership
boundary between services. This ADR is the **constitution** every domain-mapping pass must follow.

## Decisions

1. **Data ownership / no-overlap (security).** Each module owns its tables (its `tables:` frontmatter).
   **Only the owning service writes those tables.** No service ever writes another domain's tables.
   Cross-domain effects happen **only** via domain events (the reacting module's own listener writes its
   own tables) or read-only queries through the owning module's service/API. Write-overlap is a security +
   integrity violation (privilege escalation across bounded contexts, corrupt audit trail). Full model:
   [[../security/data-ownership]].

2. **Single owner per company.** A company has **exactly one** `owner`. Ownership is transferable, never
   duplicated. Enforced at the data + service layer. See [[../domains/core/rbac/_module]].

3. **Module-scoped role permissions.** The owner (and any role with `core.rbac.*`) can create custom
   roles and assign permissions — **but only permissions belonging to modules the company currently has
   active**. Deactivating a module removes its permissions from the assignable set and suspends them on
   existing roles. See [[../domains/core/rbac/_module]] + [[../infrastructure/module-catalog]].

4. **Every feature specifies its UI.** Each `feature` note carries a `## UI` block classifying the surface
   as one of: **simple Filament resource** · **custom Filament page** · **Filament widget** · **Vue/Inertia
   (public/portal)** · **background/none** — plus how the page looks and behaves (layout, key interactions,
   empty/loading/error/selected states, permission gating). Convention + template:
   [[../architecture/patterns/feature-ui-spec]].

5. **Cross-domain relations are explicit.** Each module/feature note carries a `## Relations` section:
   which domains it consumes from / feeds (via which events or read APIs), and shared reference entities.
   Overview + matrix: [[../architecture/cross-domain-relations]].

6. **Per-domain opportunity research.** Each domain gets an `_opportunities.md` note: web-researched tooling
   gaps / features users ask for online that competitors lack — candidate differentiators. Sourced +
   dated; speculative items marked UNVERIFIED.

7. **Feature note structure** follows [[../_meta/feature-template]].

## Consequences

- All 31 domains get the full entity tree (module → feature) with these five sections everywhere.
- Arch-test rule (for the eventual build): a service references only its own domain's Models.
- Bigger vault; the payoff is genuinely build-ready, security-bounded specs.

## Related

- [[../security/data-ownership]] · [[../architecture/patterns/feature-ui-spec]] · [[../architecture/cross-domain-relations]]
- [[decision-2026-06-20-workspace-hub-and-login-model]] · [[decision-2026-06-20-app-project-removed]]
