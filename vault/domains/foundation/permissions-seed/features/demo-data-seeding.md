---
domain: foundation
module: permissions-seed
feature: demo-data-seeding
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Demo-Data Seeding (`LocalDevSeeder`, non-prod)

A single non-production seeder that stands up a fully-populated "FlowFlex Demo" company + working logins, so a fresh clone opens onto a realistic product, not an empty shell.

## Behaviour

- Runs **only** when `! app()->environment('production')`; throws `RuntimeException` if forced in prod.
- Creates the "FlowFlex Demo" company (active, setup complete), the `owner` role synced to every `web`-guard permission, free core modules, all catalog modules active (billing rows only), 5 demo users, 3 months billing history.
- Seeds three logins: `admin@flowflex.nl`/`password` (staff), `demo@flowflex.nl`/`password` (demo owner), `test@test.nl`/`test1234` (dual staff + tenant owner — the real working login).
- Owner auto-receives newly seeded permissions via `syncPermissions`.
- Convention: every new domain phase adds a realistic section to the demo seeder ([[../../../../decisions/decision-2026-06-20-full-mapping-conventions]] + LocalDemoDataSeeder convention).

## UI

- **Kind**: background (artisan seeder — no screen). Its output is what every other module's screens display
  in local dev / demos.

## Data

- Owns: writes demo rows across foundation/core tables it controls. As domains rebuild, each seeds its own demo
  section (never another domain's tables — [[../../../../security/data-ownership]]).
- Cross-domain writes: each domain's demo section writes only its own tables.

## Relations

- Consumes: nothing. Feeds: realistic first-run experience, QA, screenshots.
- Shared entity: the "FlowFlex Demo" company as the canonical demo tenant.

## Test Checklist

### Unit
- [x] Owner `syncPermissions` grants every current `web`-guard permission

### Feature (Pest)
- [x] `LocalDevSeeder` throws `RuntimeException` in production; seeds the demo company in non-prod
- [x] The three demo logins authenticate; `test@test.nl` is both staff admin and tenant owner

## Unknowns

> [!warning] UNVERIFIED — full `ModuleCatalogSeeder` active-module list; prod first-tenant bootstrap flow (no
> public registration — staff-created). See [[../unknowns]].

## Related

- [[../_module|Permissions Seeder]] · [[permission-seeding]] · [[../../../../architecture/patterns/seeders]] · [[../../_opportunities]]
