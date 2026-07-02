---
type: architecture
category: process
pattern-key: dod
status: stable
last-reviewed: 2026-07-02
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
| Tests | `docker exec flowflex_app php artisan test` | all green, no skipped tenant-isolation tests; per-feature + rollup checklists covered |
| Browser smoke (panels touched) | `npx playwright test tests/Browser/{Panel}` | shell renders, nav sweep, zero console errors ([[patterns/testing-pattern]]) |
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
11. Every resource passes the [[patterns/filament-resource-checklist]] — real Section-wrapped form (or documented `canCreate(false)`), create/edit path, badges/filters, global-search attributes on customer-facing entities. *(Added 2026-06-12 — 27 resources shipped unusable because no gate checked this.)*
12. Panel dashboards have widgets (stats + chart minimum, PHP date grouping) and LocalDevSeeder feeds them realistic demo data — empty dashboards read as broken
13. Live smoke after every panel-touching session (`/flowflex:verify`): real login, key pages 200, one scripted Livewire POST — the Pest suite has stayed green through multiple browser-breaking bugs ([[patterns/tenant-context-pitfalls]]); GET-only checks are not verification. **Functional browser smoke is now automated** (item 20, [[patterns/testing-pattern]] Playwright suite); the **manual Playwright screenshot is required only for visual/design review of new or restyled screens** — GET-200 + green Pest does not prove the UI *looks* correct (proven repeatedly 2026-06-14: blank dashboards, doubled logos, broken layout, silent no-op saves all passed).
14. UX states designed ([[patterns/ux-states]]): every table has a human empty state with an action (override the platform default when the module can say something better); forms >8 fields are wizard steps that validate per step; errors read human, never log-file
15. Switchboard+ conformance ([[../frontend/design-system|design-system]] + [[patterns/filament-panel-chrome]]): panel artifacts use the shared skin (no per-panel CSS forks, no hardcoded domain colors); any new theme CSS selector verified against rendered Filament 5 markup ([[filament-patterns]] item 16); global-search attributes added so the module's records appear in Spotlight (⌘K)
16. Business-record models add `LogsCompanyActivity` so changes feed the audit log + Recent-activity feed ([[patterns/belongs-to-company]] audit section) — a record a user creates/edits but that never appears in the audit log is a gap
17. Concurrency handled: the spec's `## Concurrency` note declares the tier per write path, and every edit surface with shared editable records implements the stale-record guard ([[patterns/optimistic-locking]]) — no silent last-write-wins
18. Error/UX states cover failure, not just success: human error messages, full-page error states, and Livewire-crash recovery per [[patterns/error-pages]] — a screen that white-screens on a thrown exception is not done
19. Per-feature Test Checklists all green — every `features/*.md` checklist passes, not just the `_module.md` rollup ([[patterns/testing-pattern]])
20. Playwright smoke passes for every touched panel/custom page ([[patterns/testing-pattern]] Browser Smoke section) — per-panel shell + nav sweep with zero console errors, per-custom-page kind interaction; this is the automated replacement for the old manual functional check in item 13

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

- [[_archive/BUILD-ORDER]] — what to build next
- [[_archive/ROADMAP]] — milestones to v1
- [[_meta/spec-template]] — spec format + `(assumed)` convention
- [[architecture/patterns/testing-pattern]] — test scope
- [[architecture/ci-cd]] — the same gates in CI
