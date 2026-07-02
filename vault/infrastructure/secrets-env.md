---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Environment & Secrets

> [!warning] `.env.example` does NOT describe the running stack
> `app/.env.example` ships **Laravel skeleton defaults** — `DB_CONNECTION=sqlite`, `CACHE/QUEUE/SESSION=database`,
> `BROADCAST=log`, `MAIL=log`, `REDIS_CLIENT=phpredis` with no password. The **real runtime drivers**
> come from the `docker-compose.yml` `x-app-env` block (pgsql + redis + meilisearch + smtp→mailpit).
> A reader who trusts `.env.example` will get the infra wrong. See [[docker-stack]] for the truth.

## Where each value really comes from

| Layer | Source of env | Drivers |
|---|---|---|
| Docker runtime | `docker-compose.yml` `x-app-env` | pgsql / redis / redis / redis / smtp→mailpit / meilisearch |
| Test suite | `app/phpunit.xml` `<php>` block | sqlite `:memory:`, cache=array, queue=sync, mail=array |
| Host CLI / `.env.example` | skeleton defaults | sqlite + `database` drivers (NOT docker) |

## Dev secrets (committed, non-sensitive)

Local-only credentials are hardcoded in compose: pg `secret`, redis `secret`, meili `masterKey`.
These are dev fixtures — fine to commit.

> [!warning] UNVERIFIED — needs confirmation: production secrets management
> No production secrets store (Vault / SSM / Doppler / cloud env) exists yet. How prod injects
> `APP_KEY`, DB/redis creds, Stripe keys, Meili key, Reverb app keys is **not provisioned**.

## Related

- [[docker-stack]] · [[ci-cd]] · [[deployment]] · [[../security/_moc|Security]] · [[_moc|Infrastructure MOC]]
