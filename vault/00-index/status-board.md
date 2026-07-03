---
type: status-board
status: wip
color: "#6B7280"
updated: 2026-06-20
---

# Status Board

Live build state, driven by the `build-status:` frontmatter on each note (requires the **Dataview**
plugin). Replaces the old hand-maintained `build/STATUS.md` (archived at [[_archive/STATUS-2026-06-14]]).

> [!info] Build-status legend
> `built` = code exists & verified · `planned` = spec only (incl. stripped rebuild targets) ·
> `deferred` = placeholder · `stripped` (historical) = was built then reverted.

## Reality snapshot (2026-06-20)

> [!important] Greenfield — the app project was removed
> The Laravel codebase, docker stack and all configs were **deleted** (repo = `vault/` + `CLAUDE.md` only).
> **Nothing is built.** The entire vault is now a blueprint/spec for a system to be built from scratch.
> See [[../decisions/decision-2026-06-20-app-project-removed]].

| Layer | State |
|---|---|
| Everything (all 31 domains, infra, security) | 📝 planned — spec/blueprint only, no code |
| Previously built (core, foundation + shell) | 📝 planned — code removed 2026-06-20; specs retained as blueprint |
| Deferred domains (10) | 💤 deferred (stub index only) |
| Production infra / CD | ⚠ UNVERIFIED — nothing provisioned |

## Recent sessions

| Date | Scope | Work |
|---|---|---|
| 2026-07-03 | foundation (all 8) | ✅ Phase 0 BUILT + live-verified: scaffold, docker (9 svc), tenancy, queues/Horizon, email, /admin + /app panels with MFA, seeders (demo logins), CI. Pest 40/40, PHPStan clean, container pgsql migrate:fresh --seed clean, both logins 200, /horizon admin-gated. Hand gate: log in at localhost:8080. |
| 2026-07-03 | All 21 domains | ✅ Vault v3 program waves 2–3b complete: Filament Artifacts + Concurrency on all 172 module specs, per-feature Test Checklists, hub normalization, [[../_meta/artifact-registry\|artifact registry]] generated, module-graph backfilled. Batches 3–4 partly done inline after subagent session-limit outage. |
| 2026-07-02 | Wave 1 + batch 0 | ADRs, patterns (optimistic-locking, error-pages, page-blueprints, custom-page-checklist), spec-template v3; legal/ai/analytics/workplace propagated |

## Live queries (populate as `build-status` frontmatter is backfilled)

```dataview
TABLE build-status AS "Status", domain AS "Domain", type AS "Type"
FROM "domains" OR "infrastructure" OR "security"
WHERE build-status
SORT build-status ASC, domain ASC
```

### Built features

```dataview
LIST
FROM "domains"
WHERE build-status = "built"
SORT file.path ASC
```

### Rebuild targets (planned, was-built)

```dataview
LIST
FROM "domains/hr" OR "domains/finance" OR "domains/crm"
WHERE type = "module"
SORT file.path ASC
```

### UNVERIFIED items needing confirmation

```dataview
TABLE file.folder AS "Area"
WHERE status = "unverified"
SORT file.path ASC
```

## Related

- [[../build/ROADMAP|Build roadmap]] — feature-level build order with per-feature AI + hand gates
- [[00-index/MOC|Vault MOC]] · [[_audit/AUDIT|Audit]] · [[_meta/module-graph|Module graph]]
