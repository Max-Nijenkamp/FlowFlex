---
type: architecture
category: process
pattern-key: dod
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Way of Working

How FlowFlex gets built: the per-module loop, quality gates, definition of done, and what to do when reality disagrees with a spec. Solo dev + AI agents, aggressive 6–12 month timeline — discipline here is what keeps 173 modules consistent.

---

## The Per-Module Loop

```
/flowflex:start {module-key}        ← briefing: spec + patterns + deps + gaps
        │
        ▼
1. Migration        — tables from spec Data Model, company_id + indexes
2. Model            — HasUlids + BelongsToCompany + SoftDeletes (+ states)
3. Factory          — incl. state variants
4. DTOs             — fields + validation from spec ## DTOs
5. Service/Action   — signatures from spec ## Services & Actions
6. Events/Listeners — payloads character-exact from event-bus contracts
7. Filament         — artifacts per spec ## Filament, kinds per ui-strategy
8. Permissions      — spec ## Permissions list → PermissionSeeder
9. Tests            — spec ## Test Checklist, all green
        │
        ▼
/flowflex:sync {module-key} status=in-progress|complete
/flowflex:done {module-key}         ← only when DoD below passes
```

Work strictly from the spec's **Build Manifest** — if a file isn't in the manifest and turns out to be needed, that's a spec gap: log it, update the spec.

---

## Quality Gates (every session, before sync)

| Gate | Command | Pass condition |
|---|---|---|
| Code style | `docker exec flowflex_app ./vendor/bin/pint --dirty` | 0 issues |
| Static analysis | `docker exec flowflex_app ./vendor/bin/phpstan analyse` | 0 errors at configured level |
| Tests | `docker exec flowflex_app php artisan test` | all green, no skipped tenant-isolation tests |
| N+1 check | Telescope queries tab on the module's list + view pages | no duplicate queries |
| TypeScript sync | `php artisan typescript:transform` (if DTOs changed) | generated.d.ts updated |
| pgsql migrate (if migrations changed) | `docker compose exec -T app php artisan migrate:fresh --seed --force` | clean — sqlite tests miss pgsql constraint ordering |

A failing gate means the session is **not done** — fix or log a gap; never sync `complete` over a red gate.

---

## Definition of Done (gates `/flowflex:done`)

A module is `complete` only when ALL of:

1. Every file in the spec's Build Manifest exists
2. Spec Test Checklist: every box checked by a real passing test (tenant isolation + module gating mandatory)
3. All quality gates green
4. `canAccess()` present on every resource/page (permission + `hasModule`)
5. Permissions seeded; `php artisan migrate:fresh --seed` runs clean
6. Events fired match spec frontmatter `fires-events`; listeners queued with `WithCompanyContext`
7. No open **high-severity** gap against this module in [[build/gaps/INDEX]]
8. Spec updated: anything that changed during build is reflected back (spec stays truth)
9. Vault synced: STATUS.md row, module-graph row still accurate
10. Perceived performance ([[patterns/perceived-performance]]): no spinners — skeleton loaders on every data view; quick actions optimistic where listed; transitions ease-out (start fast, end slow)

---

## Spec-Deviation Protocol

Specs contain `*(assumed)*` markers for invented defaults (see [[_meta/spec-template]]).

| Situation | Action |
|---|---|
| `*(assumed)*` value works fine | Build it as written; remove marker on next spec touch |
| `*(assumed)*` value is wrong for this case | Override + log `/flowflex:decision` if cross-cutting, or just update spec + note if local |
| Spec contradicts an architecture doc | Architecture doc wins; fix spec; log `/flowflex:bug` |
| Spec missing something build-blocking | `/flowflex:bug "..." module={key} severity=high`, decide, update spec, then build |
| New pattern needed not in any architecture doc | STOP — write/extend the architecture doc first, then ADR if it's a choice, then build |

**Never** silently diverge from the spec. The vault must always describe what the code does.

---

## Session Hygiene

- One module per session where possible; never two domains in one session
- Start: `/flowflex:start` — always, even mid-module ("I remember the spec" is how drift starts)
- End: `/flowflex:sync` — always, even for 20-minute sessions
- Commit per module step or per session, message references module-key
- Decisions made in chat but not logged with `/flowflex:decision` don't exist

---

## Related

- [[build/BUILD-ORDER]] — what to build next
- [[build/ROADMAP]] — milestones to v1
- [[_meta/spec-template]] — spec format + `(assumed)` convention
- [[architecture/patterns/testing-pattern]] — test scope
- [[architecture/ci-cd]] — the same gates in CI
