---
type: adr
date: 2026-05-09
status: decided
module: project-scaffolding
domain: Foundation
color: "#F97316"
---

# Decision: Upgrade to Filament 5 (v5.6.2)

## Context

Phase 0 was initially built with Filament 4 (v4.11.2) because Filament 5 appeared unavailable at build time. Before starting Phase 1 domain builds, Filament 5 (v5.6.2) was confirmed available and the upgrade was performed.

## Decision

Use Filament 5 (`^5.0`, v5.6.2) for all domain builds going forward.

## Upgrade Result

- `composer require filament/filament:^5.0 -W` resolved to v5.6.2 with no dependency conflicts
- Both panels (`/admin`, `/app`) boot and route correctly on Filament 5
- All routes verified via `php artisan route:list`
- **No code changes required** — Filament 5 retained the `Schema $schema` API used in all Phase 0 resources

## Filament 5 API Patterns (use in all subsequent builds)

| Area | Pattern |
|---|---|
| `form()` method | `public static function form(Schema $schema): Schema` |
| `Section` import | `Filament\Schemas\Components\Section` |
| `Schema` import | `Filament\Schemas\Schema` |
| Panel auth model | Inferred from guard provider in `config/auth.php` — no `->authModel()` needed |
| Navigation group | Use `getNavigationGroup(): string` method override |
| Page view | Use `getView(): string` method override (non-static) |
| Form field components | `Filament\Forms\Components\*` |

## Consequences

- All Phase 1+ domain builds use Filament 5 natively
- No Filament 3 compatibility concerns (incompatible with Laravel 13)
- `composer.json` constraint: `"filament/filament": "^5.0"`

## Related Left Brain

- [[project-scaffolding]]
- [[admin-panel-flowflex]]
- [[workspace-panel]]
- [[tech-stack]]
