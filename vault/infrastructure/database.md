---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Database

Two real databases by context: **PostgreSQL 17** at runtime, **SQLite `:memory:`** for tests.

| Context | Connection | Where |
|---|---|---|
| Docker runtime (browser) | `pgsql` → `postgres:5432` db `flowflex` (user `flowflex`, pw `secret`) | `docker-compose.yml` `x-app-env` |
| Test suite | `sqlite` `:memory:` | `app/phpunit.xml` (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) |
| Host artisan CLI | `sqlite` file `app/database/database.sqlite` | `app/config/database.php` default `env('DB_CONNECTION','sqlite')` |

`app/config/database.php` defines connections for `sqlite`, `mysql`, `pgsql`, `redis`. The
default is SQLite, so **host-run `php artisan` hits the local SQLite file, not the container
PostgreSQL** — to touch the running DB use `docker compose exec app php artisan …`.

> [!warning] pgsql vs sqlite divergence
> PostgreSQL rejects self-referencing `constrained()` inside `Schema::create` (SQLite tolerates it).
> Self-FKs must be added via a post-create `Schema::table` alter. Run `migrate:fresh` against the
> docker pgsql stack periodically — the suite only exercises SQLite. See
> [[../build/gaps/gap-pgsql-self-fk-ordering]].

## Tables today (platform shell)

After the [[../decisions/decision-2026-06-19-strip-to-app-admin-shell|strip]], only platform tables
exist — `companies, users, admins, user_invitations, billing_invoices, billing_invoice_lines,
company_module_subscriptions, roles/permissions (spatie), settings, activity_log, media,
notifications, notification_preferences, webhook_endpoints, webhook_deliveries, data_imports,
consent_logs, dsar_requests` + framework tables (cache, jobs, sessions, pulse, health). **No
`hr_/finance_/crm_` tables remain.** Data model: [[../domains/foundation/multi-tenancy-layer/_module]],
[[../architecture/data-model]].

## Related

- [[docker-stack]] · [[cache-redis]] · [[../security/tenancy-isolation]]
- [[../domains/foundation/laravel-scaffold/_module]] · [[_moc|Infrastructure MOC]]
