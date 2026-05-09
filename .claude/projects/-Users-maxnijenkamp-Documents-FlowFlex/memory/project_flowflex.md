---
name: FlowFlex project context
description: Core FlowFlex project state, Phase 0 completion, tech stack, and critical patterns discovered
type: project
---

Laravel 13 + Vue/Inertia + Filament 5 (v5.6.2) SaaS. Two panels: `/admin` (Admin model, admin guard) and `/app` (User model, web guard). PostgreSQL 17 + Redis 8 + Horizon + Reverb. ULID PKs everywhere. 32 domains, 313 modules total planned.

**Phase 0: COMPLETE** (5/5 modules: project-scaffolding, admin-panel, workspace-panel, docker-local-environment, testing-standards)
**Phase 1: Core Platform — NOT STARTED** (12 modules, migration range 010000–099999)

**Why:** Phase 0 must be 100% before Phase 1 begins. All 74 tests pass. Test gate confirmed.

**How to apply:** Phase 1 can begin. The right-brain is synced. Test suite is the quality gate.

## Critical patterns confirmed in Phase 0

- `SetCompanyContext` MUST be in `->authMiddleware([...])` in every Filament panel that serves tenant users. Not just global web middleware.
- Spatie Permission ULID fix: all team_foreign_key and model_morph_key columns use `string(26)` not `unsignedBigInteger`
- Filament 5 login test: `Livewire::test(Login::class)->set('data.email', x)->set('data.password', y)->call('authenticate')`
- Before each Livewire login test: `Filament::setCurrentPanel(Filament::getPanel('id'))` and `auth()->guard()->logout()`
- Rate limiter cleared globally in Pest.php beforeEach (Filament Login rate-limits to 5 attempts per IP)
- `->placeholder('Never')` not `->default('Never')` on dateTime() columns
