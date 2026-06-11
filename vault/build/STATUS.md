---
type: build-status
last-updated: 2026-06-10
color: "#F97316"
---

# Build Status

Per-domain progress. Update `status` frontmatter in module specs — this table is the manual view (Dataview query below auto-populates if Dataview plugin is installed).

**Last updated:** 2026-06-11 (vault remapped to v2 — all 173 specs implementation-ready; build not started)

---

## Progress by Domain

| Phase | Domain | Built | Total | Progress |
|---|---|---|---|---|
| MVP | Foundation | 0 | 8 | 🔴 0% |
| MVP | Core Platform | 0 | 15 | 🔴 0% |
| MVP | HR & People | 0 | 15 | 🔴 0% |
| MVP | Finance & Accounting | 0 | 13 | 🔴 0% |
| MVP | CRM & Sales | 0 | 15 | 🔴 0% |
| Phase 2 | Projects & Work | 0 | 11 | 🔴 0% |
| Phase 2 | Support & Help Desk | 0 | 7 | 🔴 0% |
| Phase 2 | Communications | 0 | 8 | 🔴 0% |
| Phase 2 | Document Management | 0 | 6 | 🔴 0% |
| Phase 3 | Marketing | 0 | 7 | 🔴 0% |
| Phase 3 | Operations | 0 | 7 | 🔴 0% |
| Phase 3 | Analytics & BI | 0 | 5 | 🔴 0% |
| Phase 3 | IT & Security | 0 | 6 | 🔴 0% |
| Phase 3 | Legal & Compliance | 0 | 6 | 🔴 0% |
| Phase 3 | E-commerce | 0 | 8 | 🔴 0% |
| Phase 3 | Learning & Development | 0 | 8 | 🔴 0% |
| Phase 3 | AI & Automation | 0 | 4 | 🔴 0% |
| Phase 3 | Customer Success | 0 | 6 | 🔴 0% |
| Phase 3 | Procurement | 0 | 6 | 🔴 0% |
| Phase 3 | Workplace & Facility | 0 | 5 | 🔴 0% |
| Phase 3 | Events Management | 0 | 7 | 🔴 0% |
| Deferred | (10 domains) | — | — | stub only |

**MVP Total: 0 / 66 modules built**
**Phase 2 Total: 0 / 32 modules built**
**Phase 3 Total: 0 / 75 modules built**
**All active: 0 / 173 modules — every Phase 1/2/3 module is fully specced**

---

## Dataview Auto-Board

> Install the Dataview plugin in Obsidian to enable the auto-updating board below.

```dataview
TABLE
  domain AS "Domain",
  status AS "Status",
  module-key AS "Key"
FROM "domains"
WHERE type = "module"
SORT domain ASC, status ASC
```

Active modules only (in-progress + complete):

```dataview
TABLE
  domain AS "Domain",
  status AS "Status",
  module-key AS "Key"
FROM "domains"
WHERE type = "module" AND (status = "in-progress" OR status = "complete")
SORT status DESC, domain ASC
```

v1 gate view (v1-core modules not yet complete):

```dataview
TABLE
  module-key AS "Key",
  status AS "Status",
  depends-on AS "Blocked by"
FROM "domains"
WHERE type = "module" AND priority = "v1-core" AND status != "complete"
SORT module-key ASC
```

---

## Recent Sessions

| Date | Domain | Modules | Notes |
|---|---|---|---|
| 2026-06-11 | All | vault-wide | Security spec-conformance audit: 173 specs / 31 domains → 184H/85M/29L. ADR-2026-06-11 (mandatory canAccess + webhook verify + guest guards + encrypted PII), template + filament-patterns hardened, 7 systemic gaps filed, 7 specs encrypted-fields fixed |
| 2026-06-11 | All | vault-wide | Audit backfill: access-contract line into 165 specs; per-spec Security notes into 79 specs (webhook/public-guard/rate-limit/upload); UI ADR rows 17–19 + 3 specs re-cited; all 7 gaps resolved; CLAUDE.md command count fixed |
| 2026-06-11 | Foundation | foundation.scaffold | Laravel 13.15 + Filament 5.6.7 scaffold: ~50 packages installed, ULID companies/users/admins migrations + models + factories, Pest 10/10 green, Pint clean. 2 gaps: Larastan crash (env), 4 Filament plugins not yet v5. Status in-progress (Larastan gate unmet) |

---

## How to Update

1. Start a module: set `status: in-progress` in the module spec frontmatter
2. Complete a module: set `status: complete` in the module spec frontmatter
3. Update the Built count in the table above
4. Add a row to Recent Sessions
5. Run `/flowflex:sync` to create a gap file or ADR if needed
