---
domain: foundation
module: laravel-scaffold
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Laravel Scaffold — Security

Parent: [[_module]]

The scaffold sets the security-relevant defaults every other module inherits. It owns no business logic but bakes in the identity, hashing, and driver choices that later become attack surface.

## Baked-in controls

| Control | Choice | Why it's a control |
|---|---|---|
| Primary keys | ULID via `HasUlids` | non-enumerable, non-sequential — no integer-ID leakage / tenant-count guessing across companies |
| Soft deletes | `SoftDeletes` everywhere | deletes are recoverable; supports audit + GDPR erasure flows ([[../../../security/data-privacy-gdpr]]) |
| Password hashing | bcrypt (Laravel default; `BCRYPT_ROUNDS=4` **only** in tests) | production rounds stay at the framework default |
| Session/cache/queue | Redis, `--requirepass` | no file-based session on shared disk; Redis is auth'd ([[../../../infrastructure/cache-redis]]) |
| DB | PostgreSQL-only | parameterised via Eloquent; pg row-level features available if needed |
| No debug helpers in `app/` | arch test bans `dd`/`dump`/`var_dump` | prevents accidental data disclosure in responses |

## Tenant-scope precondition

The scaffold defines `companies` + `users(company_id)` but does **not** itself enforce scoping — that is the
[[../multi-tenancy-layer/_module|multi-tenancy layer]]. The scaffold's job is only to guarantee every tenant table
carries a `company_id` foreign key so the scope has something to filter on. A tenant model created without
`company_id` is the classic isolation hole; the arch tests ([[../test-suite/_module|test-suite]]) catch it.

> [!warning] UNVERIFIED — needs confirmation
> Production `BCRYPT_ROUNDS`, `APP_KEY` rotation policy, and whether `SESSION_SECURE_COOKIE`/`SESSION_ENCRYPT`
> are forced on in production were not read from a live `config/` here — the test override (`rounds=4`) is
> confirmed; production values are assumed framework defaults.

## Related

- [[_module]] · [[data-model]] · [[unknowns]]
- [[../../../security/tenancy-isolation]] · [[../../../security/encryption]] · [[../multi-tenancy-layer/_module]]
