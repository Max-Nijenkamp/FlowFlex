---
domain: foundation
module: laravel-scaffold
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Laravel Scaffold — Unknowns

Parent: [[_module]]. Open questions and unconfirmed facts. App was removed 2026-06-20 ([[../../../decisions/decision-2026-06-20-app-project-removed]]); these need re-derivation from a live tree on rebuild.

| # | Item | State | Resolve by |
|---|---|---|---|
| 1 | Exact installed package set | ✅ RESOLVED 2026-07-03 — manifest installed on rebuild (`app/composer.lock`); `lorisleiva/laravel-actions` + `brick/money` needed `--with-all-dependencies`; fullcalendar/tiptap plugins skipped per [[../../../decisions/decision-2026-07-03-custom-over-missing-plugins]] | — |
| 2 | Production `BCRYPT_ROUNDS`, `SESSION_SECURE_COOKIE`, `SESSION_ENCRYPT` | UNVERIFIED — only the `rounds=4` test override is confirmed | Read live `config/` |
| 3 | `strict_types=1` enforcement | ✅ RESOLVED 2026-07-03 — `tests/Architecture/LayersTest.php` asserts `toUseStrictTypes()` on all of `App` | — |
| 4 | `password_reset_tokens` + `sessions` shapes | ✅ RESOLVED 2026-07-03 — Laravel-standard shapes in `0001_01_01_000001_create_users_table.php` (`sessions.user_id` is `foreignUlid`) | — |
| 5 | Whether PHP floor moves to 8.4 when 8.3 EOLs | Open decision — CI already tests 8.3/8.4/8.5 | Track PHP EOL |

## Related

- [[_module]] · [[security]] · [[infrastructure]]
- [[../../../decisions/decision-2026-06-20-app-project-removed]]
