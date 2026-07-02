---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Deployment (Production)

> [!warning] UNVERIFIED — production infrastructure does not exist yet
> There is **no** terraform / cloud config / deploy pipeline in the repo. Everything on this page is a
> **planned target**, not provisioned reality. Per the project ground-truth ruling, infra truth =
> the local docker stack ([[docker-stack]]); production is aspirational until configs land.

## Intended target (from `architecture/deployment.md` — aspirational)

- Laravel 13 / PHP-FPM app behind a web server, PostgreSQL, Redis, Meilisearch, Reverb.
- Horizon + scheduler as long-running workers.
- Prod mail via Resend/Postmark ([[mail]]).
- Zero-downtime release flow.

What must be decided/provisioned before this is real: hosting target (Fly/AWS/GCP/…), IaC, secrets
store ([[secrets-env]]), a CD stage on top of [[ci-cd]], domain/TLS, backups (spatie/laravel-backup is
installed), Reverb host port + TLS.

## Related

- [[ci-cd]] · [[secrets-env]] · [[docker-stack]] · [[../architecture/deployment]] · [[_moc|Infrastructure MOC]]
