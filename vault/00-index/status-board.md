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

- [[00-index/MOC|Vault MOC]] · [[_audit/AUDIT|Audit]] · [[_meta/module-graph|Module graph]]
