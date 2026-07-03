---
type: roadmap
status: active
color: "#F97316"
updated: 2026-07-03
---

# FlowFlex Build Roadmap — feature by feature

Greenfield build order for the whole product, one tickable feature at a time. Replaces the archived milestone roadmap (`_archive/ROADMAP.md`) — that one tracked the pre-2026-06-20 app, which was deleted.

## How this roadmap works

Every feature row carries two acceptance gates. A box gets ticked only when **both** pass:

1. **AI gate** (Claude does it): `/flowflex:start {module-key}` → build the feature per spec → the feature's `## Test Checklist` (Unit / Feature / Livewire) is covered by green Pest tests → Pint + PHPStan green → `/flowflex:verify` HTTP smoke passes.
2. **Hand gate** (you do it): the `hand-check:` on the row — a concrete route to open and interaction to perform in the browser. If it doesn't behave as the spec's UI section says, it's not done; log it with `/flowflex:bug`.

Working loop per module:

```
/flowflex:start {key}   → briefing (spec, deps, patterns, permissions)
  build features top-to-bottom, ticking AI gate per feature
you: run the hand-checks for the module
/flowflex:sync {key}    → vault updated (every session)
/flowflex:done {key}    → only when every box + both gates are green
```

Rules: never start a module whose **Hard deps** aren't complete; one module per session where possible (small modules can pair); hand-checks are starting points scraped from each feature spec's UI section — the spec is authoritative if they disagree.

## Phases

| Phase | Scope | Modules | Features | You can, after it |
|---|---|---|---|---|
| [[roadmap/phase-0-foundation\|0 — Foundation]] | Laravel, docker, tenancy, queues, email, panels, permission seed, test suite | 8 | 17 | boot the stack + log in to /admin and /app |
| [[roadmap/phase-1-core-platform\|1 — Core platform]] | billing/gating, RBAC, invitations, settings, files, notifications, audit, marketplace, staff console, setup wizard | 12 | 32 | onboard a company end-to-end, activate modules |
| [[roadmap/phase-2-first-business-slice\|2 — First business slice]] | HR profiles/leave/self-service-core, Finance ledger/invoicing, CRM contacts/deals/pipeline/activities | 11 | 35 | run a real small company on it (smallest sellable slice) |
| [[roadmap/phase-3-v1-completions\|3 — v1 completions]] | remaining core + full HR / Finance / CRM | 40 | 120 | full v1 for the three anchor domains |
| [[roadmap/phase-4-p2\|4 — p2 domains]] | Projects, Support, Communications, DMS | 31 | 83 | expand into delivery + service teams |
| [[roadmap/phase-5-p3\|5 — p3 + later]] | Marketing, Operations, Procurement, IT, Legal, Analytics, AI, LMS, Customer Success, E-commerce, Events, Workplace | 76 | 240 | the full 21-domain suite |

**Total: 178 modules · 527 features.** Phase files are generated from the specs (2026-07-03) — regenerate after spec restructures rather than hand-editing rows; ticks survive a regenerate only if you re-apply them, so prefer regenerating between modules, not mid-module.

## Exit gate per phase

A phase is done when every module in it passed `/flowflex:done` (which enforces the full Definition of Done in [[../architecture/way-of-working]]), plus one full-suite run: `migrate:fresh --seed` clean, Pest suite green, `/flowflex:verify` sweep across the phase's panels.

## Porting lessons from the deleted app (apply during build, don't rediscover)

From `_archive/build-history/` — code-level traps the previous build hit:

- **pgsql self-FKs**: define self-referencing FKs in post-create `Schema::table` alters, never inside `Schema::create` (sqlite passes, pgsql breaks).
- **notifications.data must be jsonb** on pgsql — Filament bell queries `data->>`.
- **Filament assets**: `filament:assets` publish must be in composer post-install/update hooks or the browser 404s while the suite stays green.
- **Panel MFA contract**: enabling panel MFA requires the auth model to implement `HasAppAuthentication` — otherwise login 500s; cover panel logins with Livewire submit tests, not just route 200s.
- **Static analysis**: plain PHPStan + `@property` docblocks, not Larastan (boot crash — ADR 2026-06-11).
- **Null-team 403 family**: after every session touching panels/auth/middleware run `/flowflex:verify` (scripted Livewire `$refresh` POST catches it) — [[../architecture/patterns/tenant-context-pitfalls]].

## Related

- [[gaps/INDEX|Open gaps]] (13 feature gaps from the 2026-07 research are backlog candidates — schedule them into their module's phase row when accepted)
- [[../00-index/status-board|Status board]] · [[../_meta/artifact-registry|Artifact registry]] · [[../_meta/module-graph|Module graph]]
- [[../architecture/way-of-working|Definition of done]] · [[../architecture/local-dev|Local dev]]
