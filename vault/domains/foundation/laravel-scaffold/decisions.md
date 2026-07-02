---
domain: foundation
module: laravel-scaffold
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Laravel Scaffold — Decisions

| Decision | Rationale | Status |
|---|---|---|
| PHP `^8.3` (not 8.4) | composer.json floor; CI matrix runs 8.3/8.4/8.5 so 8.3 is the support floor | verified |
| ULID PKs everywhere | sortable, non-enumerable, no integer-ID leakage across tenants | verified |
| No auth starter kit | Filament 5 owns all authentication (login, 2FA, password reset, email verify) | verified |
| Redis for cache/queue/session | single in-memory backend; Horizon needs Redis anyway | verified |
| PostgreSQL-only | no MySQL fallback; uses pg-specific features | verified |
| Flat foldering | no `Core/`/`Foundation/` namespaces — flat or real-domain only (ADR 2026-06-11) | verified |
| `first_name`/`last_name` split | not a single `name` column | verified |

## Related

- [[_module|Laravel Scaffold]]
- [[../../../architecture/way-of-working]]
