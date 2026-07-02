---
domain: core
module: two-factor-auth
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Two-Factor Auth — Unknowns / UNVERIFIED

Parent: [[_module]]

## `two_factor_enabled` column

> [!warning] UNVERIFIED — needs confirmation: whether `users` / `admins` carry a `two_factor_enabled` boolean.
> Only seen in the admins-table migration's `down()` (drops `two_factor_enabled`); no matching `up()` add was observed. Treated as `*(assumed)*` present. Confirm the column, its table(s), and default.

## Assumed metadata

- **module-key** `core.2fa` *(assumed)* — no flat spec assigned one.
- **priority** `v1-core` *(assumed)* — inferred from being wired into both production panels.
- Soft-dep on [[../notifications/_module]] for enrollment/recovery notices *(assumed)* — not confirmed in code.

## Assumed file paths

> [!warning] UNVERIFIED — needs confirmation: `AppPanelProvider` / `AdminPanelProvider` full paths under `app/Providers/Filament/`. Registration lines confirmed (~49 / ~43); enclosing file paths assumed conventional.

## Related

- [[_module]] · [[data-model]] · [[decisions]]
