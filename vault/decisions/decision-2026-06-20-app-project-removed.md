---
type: adr
date: 2026-06-20
status: decided
domain: All
color: "#F97316"
updated: 2026-06-20
---

# App project removed — vault is now a greenfield blueprint

## Context

The Laravel/Filament application (`app/`), the docker stack (`docker-compose.yml`, `docker/`) and all
configs were **deleted from the repo**. What remains is the vault (`vault/`) and `CLAUDE.md`. The
previous state — a built platform shell (core + foundation, Admin/App/Auth panels, ~186 tests) plus the
stripped HR/Finance/CRM — no longer exists as code.

This supersedes the incremental history: [[decision-2026-06-19-strip-to-app-admin-shell|the strip to the app/admin shell]]
removed the domains; this removes everything.

## Decision

Treat the vault as a **pure spec/blueprint for a system to be built from scratch**. Concretely:

- Every note is `build-status: planned` (or `deferred` for stub domains). Nothing is `built`.
- `status:` reset from `verified` → `unverified` — there is no codebase to verify against.
- The `infrastructure/` notes are retained as the **last-known-good stack captured before deletion** and
  serve as the faithful rebuild target (not a running system).
- The five exploded domains (core, foundation, hr, finance, crm) keep their entity-tree structure as the
  most detailed blueprints; the other 26 stay single-spec.

## Consequences

- "Reality wins" now means "the spec is the reality" everywhere — there is no code ground-truth until a
  rebuild starts. New claims cannot be code-verified; mark uncertain ones UNVERIFIED.
- Build order starts at the platform: build [[../domains/core/_index|core]] + [[../domains/foundation/_index|foundation]]
  first, then domains.
- `CLAUDE.md` still documents the old `app/` layout + `/flowflex:*` workflow; it describes the intended
  rebuild target, not present files.

## Related

- [[decision-2026-06-19-strip-to-app-admin-shell]] · [[../00-index/status-board]] · [[../infrastructure/_moc]]
