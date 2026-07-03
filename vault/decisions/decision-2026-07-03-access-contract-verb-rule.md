---
type: adr
date: 2026-07-03
status: decided
domain: All
color: "#F97316"
---

# Access-contract verbs must exist in the module's Permissions table

## Context

Wave 2 v3 propagation found 9+ modules whose `canAccess()` contract gated on a permission verb their own `security.md` `## Permissions` table never defined (support.live-chat, five communications modules, crm forecasting/pipeline/price-management). A built module gating on a never-seeded verb renders its panel invisible for everyone — or tempts an ad-hoc seed outside the registry. Tracked as [[../_archive/build-history/gap-canaccess-verb-not-in-permission-table|gap]]; all known instances were reconciled during propagation.

## Options Considered

1. One-time fix only — rejected: nothing stops the next spec from reintroducing the drift.
2. Consistency rule + repeatable lint — chosen.

## Decision

**Rule:** every permission verb cited in a module's access contract (`can('…')` in `architecture.md`) and in feature Gating lines must appear in that module's `security.md` `## Permissions` table. Added to the [[../architecture/way-of-working]] quality gates.

**Lint** (run at `/flowflex:start` briefing and before `/flowflex:done`): grep the module's `can('x')` citations and assert each `x` appears in `security.md`. A vault-wide sweep ran at ratification (2026-07-03) — remaining hits fixed or explicitly annotated.

## Consequences

- Spec-side drift caught before build; the seeder registry ([[../architecture/patterns/seeders]]) stays the single source of verbs.
- Feature-note *(assumed)* permission names must be reconciled to the security.md table before their module is marked complete.

## Related

- [[../architecture/way-of-working]] · [[../architecture/patterns/policy]] · [[decision-2026-07-03-public-endpoint-limiters]]
