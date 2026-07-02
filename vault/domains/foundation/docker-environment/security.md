---
domain: foundation
module: docker-environment
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Docker Environment — Security

Parent: [[_module]]. This is the **local-dev** stack; it is not the production topology ([[../../../infrastructure/deployment]]). Its "security" is mostly about not leaking dev services to the host network and not shipping dev defaults to prod.

## Local-dev posture

| Concern | Local-dev choice | Note |
|---|---|---|
| Published host ports | only `nginx 8080` + `postgres 5432` | `redis`, `mailpit`, `reverb`, `meilisearch` are `expose`-only / internal — smaller host attack surface |
| Redis auth | `--requirepass secret` | even locally Redis is password-gated ([[../../../infrastructure/cache-redis]]) |
| Mail | Mailpit captures **all** outbound mail | no real mail leaves the dev box; UI on internal `8025` |
| Secrets | `.env` (dev values only) | never the prod secret set — [[../../../infrastructure/secrets-env]] |

## Do-not-ship-to-prod

> The dev compose values (`--requirepass secret`, Mailpit, Meilisearch master key, `APP_DEBUG=true`, seeded
> demo logins with weak passwords) are **dev-only**. Production uses [[../../../infrastructure/deployment]] +
> [[../../../infrastructure/secrets-env]]; the `LocalDevSeeder` refuses to run in production
> ([[../permissions-seed/_module|permissions-seed]]).

> [!warning] UNVERIFIED — needs confirmation
> Whether the dev `postgres` publishes `5432` to `0.0.0.0` or `127.0.0.1` (host-exposure scope), and the
> Meilisearch master-key handling in dev, were not read line-by-line here — see the authoritative
> [[../../../infrastructure/docker-stack]].

## Related

- [[_module]] · [[unknowns]] · [[../../../infrastructure/docker-stack]]
- [[../../../infrastructure/secrets-env]] · [[../../../infrastructure/deployment]]
