---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Infrastructure — Map of Content

Shared, cross-feature infrastructure — **the target stack to build**. The app project (Laravel +
docker + configs) was removed 2026-06-20, so none of this is running today; these notes are the
blueprint. Production infra was never provisioned (marked `> [!warning] UNVERIFIED`).

> [!info] Provenance
> These notes were captured from the real `docker-compose.yml` + `docker/` + `app/config/*` +
> `.github/workflows/*` on 2026-06-20 **before** the codebase was deleted — so they are an accurate
> record of the last-known-good stack and a faithful rebuild target. See
> [[../decisions/decision-2026-06-20-app-project-removed]].

## Notes

- [[docker-stack]] — the 9-service local stack (topology + ports + volumes)
- [[database]] — PostgreSQL 17 (runtime) + SQLite (tests)
- [[cache-redis]] — Redis 8 cache / session
- [[queue-horizon]] — Redis queues + Horizon worker + scheduler
- [[search-meilisearch]] — Meilisearch 1.10 + Laravel Scout
- [[websockets-reverb]] — Laravel Reverb realtime
- [[mail]] — Mailpit (dev) / SMTP
- [[module-catalog]] — the `config/flowflex.php` module catalog (billing/marketing metadata)
- [[secrets-env]] — environment variables, the `.env.example` vs docker-runtime gap
- [[ci-cd]] — GitHub Actions: lint + tests
- [[deployment]] — production target (UNVERIFIED — not provisioned)

## Related

- [[../security/_moc|Security]] · [[../architecture/_moc|Architecture]]
- [[../domains/foundation/_index|Foundation domain]] (owns the platform shell that runs on this infra)
