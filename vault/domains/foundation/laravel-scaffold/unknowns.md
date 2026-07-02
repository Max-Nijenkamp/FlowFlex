---
domain: foundation
module: laravel-scaffold
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Laravel Scaffold — Unknowns

Parent: [[_module]]. Open questions and unconfirmed facts. App was removed 2026-06-20 ([[../../../decisions/decision-2026-06-20-app-project-removed]]); these need re-derivation from a live tree on rebuild.

| # | Item | State | Resolve by |
|---|---|---|---|
| 1 | Exact installed package set (full `composer.lock`) | UNVERIFIED — [[infrastructure]] lists the *intended* manifest only | Re-derive from lock on rebuild |
| 2 | Production `BCRYPT_ROUNDS`, `SESSION_SECURE_COOKIE`, `SESSION_ENCRYPT` | UNVERIFIED — only the `rounds=4` test override is confirmed | Read live `config/` |
| 3 | Whether `strict_types=1` is enforced everywhere (arch test?) | *(assumed)* — stated in `_module` intro, no test cited | Add/confirm arch test |
| 4 | `password_reset_tokens` + `sessions` column shapes | Noted in [[data-model]] as "same migration"; not tabulated | Tabulate on rebuild |
| 5 | Whether PHP floor moves to 8.4 when 8.3 EOLs | Open decision — CI already tests 8.3/8.4/8.5 | Track PHP EOL |

## Related

- [[_module]] · [[security]] · [[infrastructure]]
- [[../../../decisions/decision-2026-06-20-app-project-removed]]
