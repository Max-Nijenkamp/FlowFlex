---
type: adr
date: 2026-06-19
status: decided
domain: All
color: "#F97316"
---

# Strip the app back to the App + Admin shell (remove HR / Finance / CRM)

## Context

Development of the three business domains (HR, Finance, CRM) had accumulated
enough problems that the founder chose to reset them rather than keep patching.
The parts that are working well — the public Switchboard+ frontend, the auth /
login screens, the tenant `/app` panel and the FlowFlex staff `/admin` panel —
are to be preserved. The domains will be rebuilt later from their (retained)
vault specs.

## Options Considered

1. **Keep the Filament UI, fix forward** — rejected; the founder explicitly
   wanted a clean slate for the domain layer.
2. **Remove only the Filament panels, keep domain models/migrations/services** —
   leaves orphaned backend code and tables with no UI; messy half-state.
3. **Full domain wipe back to the App + Admin shell** — chosen.

## Decision

Remove everything CRM / Finance / HR across every layer, leaving only the
platform shell:

- **Deleted** (470 files): `app/{Actions,Contracts,Data,Events,Exceptions,Filament,Listeners,Models,Services,States}/{CRM,Finance,HR}`, the three `*PanelProvider`s + three domain `*ServiceProvider`s, the five `Api/V1` domain controllers, the public `CareersController` + `QuoteAcceptController`, all `*_create_{hr,finance,crm}_*` migrations, `database/factories/{CRM,Finance,HR}`, `tests/Feature/{CRM,Finance,HR}` + `tests/Feature/Api/V1ApiTest`.
- **Kept**: public Vue+Inertia marketing site, login/auth (`Filament/Auth`), the
  `/app` tenant panel, the `/admin` staff panel, all platform models
  (User, Company, Role, BillingInvoice, ModuleCatalog, …) and the
  `billing_invoices` table.
- **Rewired**: `bootstrap/providers.php` (5 domain providers removed),
  `routes/api.php` + `routes/web.php` (domain + careers/quote routes removed),
  `LocalDevSeeder` (domain demo data stripped — keeps `test@test.nl` staff admin,
  the FlowFlex Demo tenant + owner + test user + billing history),
  `PermissionSeeder` (domain permission strings removed, core set retained),
  `SidebarFooter` (panel-switcher chips reduced to APP only),
  `tests/Architecture/LayersTest` + `PanelAuthTest` (domain assertions repointed
  to `/app`), `phpstan.neon` (5 ignore patterns that targeted deleted Finance
  code removed).

### Deliberately NOT removed

- **`config/flowflex.php` modules** — the public marketing site (pricing page,
  per-domain product pages) reads this catalog. It is pure metadata with no code
  link to the deleted classes, so it stays to keep the frontend intact. The App
  marketplace lists these as future modules; activating one writes a billing row
  only (no domain code required).
- **The HR / Finance / CRM module specs** under `vault/domains/` — retained as
  the rebuild blueprint. Their `status:` frontmatter is intentionally left as-is.

## Consequences

- Gates green after the strip: 186 Pest tests pass, Pint clean, PHPStan level 5
  zero errors, `migrate:fresh --seed` clean on the in-memory sqlite suite.
- The demo tenant now ships empty of business data — `/app` and `/admin` render
  the platform shell only.
- Rebuilding a domain means re-running `/flowflex:start {module-key}` against the
  retained spec and re-adding its panel provider + migrations + routes.
- Work landed on branch `chore/strip-to-app-admin-shell` (not yet committed).

## Related

- [[build/decisions/decision-2026-06-12-switchboard-plus-design-system|Switchboard+ design system]] (the frontend being preserved)
- [[build/decisions/decision-2026-06-11-flat-namespace-foldering|Flat namespace foldering]]
