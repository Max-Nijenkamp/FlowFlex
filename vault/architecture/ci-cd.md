---
type: architecture
category: ci-cd
color: "#A78BFA"
---

# CI/CD Pipeline

GitHub Actions pipeline for FlowFlex. Runs on every push and PR. Blocks merge on failure.

---

## Pipeline Overview

```
Push / PR → [Lint] → [Static Analysis] → [Tests] → [Build] → [Deploy (main only)]
```

All jobs run in parallel where possible. Tests are the bottleneck — target under 3 minutes.

---

## Workflow: Pull Request

`.github/workflows/pr.yml`:

```yaml
name: PR Checks

on:
  pull_request:
    branches: [main, develop]

jobs:
  lint:
    name: Code Style (Pint)
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer install --no-interaction --prefer-dist
      - run: ./vendor/bin/pint --test

  static-analysis:
    name: Static Analysis (Larastan)
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer install --no-interaction --prefer-dist
      - run: cp .env.testing .env
      - run: ./vendor/bin/phpstan analyse --memory-limit=512M

  tests:
    name: Tests (Pest)
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo_sqlite, sqlite3
          coverage: pcov
      - run: composer install --no-interaction --prefer-dist
      - run: cp .env.testing .env
      - run: php artisan key:generate
      - run: php artisan migrate --env=testing
      - run: ./vendor/bin/pest --parallel --coverage --min=80

  security-audit:
    name: Dependency Audit
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer audit
```

---

## Workflow: Deploy to Production

`.github/workflows/deploy.yml` — triggers on merge to `main`:

```yaml
name: Deploy Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    name: Deploy
    runs-on: ubuntu-latest
    environment: production
    needs: [] # no local tests — PR checks already passed

    steps:
      - uses: actions/checkout@v4

      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.DEPLOY_HOST }}
          username: deploy
          key: ${{ secrets.DEPLOY_SSH_KEY }}
          script: |
            cd /var/www/flowflex
            php artisan down --retry=60
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan event:cache
            php artisan queue:restart
            php artisan up
            php artisan health:check
```

---

## Test Environment Config

`.env.testing`:

```env
APP_ENV=testing
APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAthisisatestkey

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array

MEILISEARCH_HOST=  # empty — search not tested in CI
```

`QUEUE_CONNECTION=sync` means queued jobs run synchronously in tests — no Horizon needed in CI.

---

## Parallel Tests

Pest parallel testing splits test files across CPU cores. Configure in `phpunit.xml`:

```xml
<extensions>
    <bootstrap class="ParaTest\Logging\TeamCity\TeamCityLogger"/>
</extensions>
```

Run: `./vendor/bin/pest --parallel`. Requires `brianium/paratest`.

Parallel tests must not share state — `RefreshDatabase` on each test class handles this for SQLite in-memory (each process gets its own in-memory database).

---

## Code Coverage

Target: 80% coverage minimum (enforced by `--min=80`). Coverage report uploaded to Codecov:

```yaml
- uses: codecov/codecov-action@v4
  with:
    token: ${{ secrets.CODECOV_TOKEN }}
    files: coverage.xml
```

Coverage priorities (highest to lowest):
1. Service classes and Actions — must have tests
2. Event listeners — test the full event chain
3. Model scopes and traits — test `BelongsToCompany`, `CompanyScope`
4. Filament resources — test `canAccess()`, create/edit form validation
5. API controllers — test 401/403/422 responses

---

## Git Branch Strategy

```
main          — production (protected, PR only)
develop       — staging (optional, if you use staging)
feature/{name} — feature branches, PR into main
fix/{name}    — bugfix branches
```

**Branch protection on `main`**:
- Require PR reviews: 0 (solo dev) or 1 (if team grows)
- Require status checks: `lint`, `static-analysis`, `tests`
- No direct push to main

---

## Pre-commit Hooks (local dev)

Optional but recommended. Use `pre-commit` or a simple shell hook:

```bash
# .git/hooks/pre-commit
#!/bin/sh
./vendor/bin/pint --test
if [ $? -ne 0 ]; then
  echo "Pint check failed. Run ./vendor/bin/pint to fix."
  exit 1
fi
```

Or use `husky` + `lint-staged` for JavaScript:

```json
// package.json
"lint-staged": {
  "*.{ts,vue}": "eslint --fix",
  "*.{ts,vue,css}": "prettier --write"
}
```

---

## Dependency Security

`composer audit` in CI checks for known CVEs in installed packages. Also run `npm audit` for JavaScript dependencies.

Set up Dependabot for automated dependency updates:

```yaml
# .github/dependabot.yml
version: 2
updates:
  - package-ecosystem: composer
    directory: /
    schedule:
      interval: weekly
    ignore:
      - dependency-name: "*"
        update-types: ["version-update:semver-major"]
  - package-ecosystem: npm
    directory: /
    schedule:
      interval: weekly
```
