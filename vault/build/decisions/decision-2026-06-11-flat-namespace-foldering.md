---
type: adr
date: 2026-06-11
status: decided
domain: All
color: "#F97316"
---

# Flat Namespace Foldering — No Core/Foundation Subfolders

## Context

Foundation and Core Platform were built with build-phase namespace folders: `app/Models/Core/`, `app/Actions/Foundation/`, `app/Services/Core/`, etc. "Core" and "Foundation" are vault build-phase labels, not product domains — in the application they created artificial separation inside what is one single platform.

## Decision

- **Build-phase folders are forbidden** in the app codebase. Everything from the Foundation + Core Platform phases lives flat: `app/Models/Activity.php`, `app/Services/BillingService.php`, `app/Actions/SendInvitationAction.php`, etc.
- **Domain folders remain allowed** for real business domains as they ship: `app/Models/Hr/`, `app/Services/Finance/`, `app/Filament/Hr/` — those reflect the product, not the build order.
- Filament panel folders (`app/Filament/App/`, `app/Filament/Admin/`) stay — they're panel routing structure, not phase labels.
- `app/Support/` stays — standard Laravel convention for cross-cutting primitives.

Applied 2026-06-11: 122 files moved, 129 files' namespaces rewritten (`App\*\Core\*` / `App\Actions\Foundation\*` → one level up), mail views flattened (`mail.core.*` → `mail.*`). All gates re-verified green (Pest 119/119, PHPStan 0, Pint clean, migrate:fresh --seed clean).

## Consequences

- Core-module spec Build Manifests in the vault reference the old `app/.../Core/...` paths — historical record, intentionally not rewritten; the code is authoritative.
- Future domain specs use `{Domain}` subfolders per CLAUDE.md (hr, finance, crm, ...) — unchanged and valid.
- CLAUDE.md directory-structure section annotated with this rule.

## Related

- [[domains/core/_index]]
- [[architecture/way-of-working]]
