---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# CI/CD — GitHub Actions

Two workflows in `.github/workflows/`, triggered on push + PR to `develop`, `main`, `master`, `workos`.
Verified from the workflow files 2026-06-20.

## `lint.yml` (job `quality`)

- PHP **8.4** (`shivammathur/setup-php`), `composer install` + `npm install`.
- Steps: `composer lint` (Pint) → `npm run format` → `npm run lint`.
- Auto-commit-fixes step present but **commented out**.

## `tests.yml` (job `ci`)

- PHP matrix **`8.3`, `8.4`, `8.5`** × Node `22`, xdebug coverage.
- `composer install --optimize-autoloader` → `cp .env.example .env` → `php artisan key:generate`
  → `npm run build` → `./vendor/bin/phpunit`.
- Tests use SQLite `:memory:` from `phpunit.xml`, so the skeleton `.env.example` is sufficient.

> [!note] Audit correction
> `architecture/ci-cd.md` claims a single PHP `8.4` pipeline; reality is a **8.3/8.4/8.5 test matrix**
> plus an 8.4 lint job. The composer floor is `^8.3` (see [[secrets-env]], AUDIT E1).

> [!warning] UNVERIFIED — needs confirmation: deployment stage
> Neither workflow deploys. There is no CD (build→ship) stage. Deployment is manual / not provisioned —
> see [[deployment]].

## Related

- [[deployment]] · [[secrets-env]] · [[../domains/foundation/test-suite/_module]] · [[_moc|Infrastructure MOC]]
